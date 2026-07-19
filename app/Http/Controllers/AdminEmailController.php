<?php

namespace App\Http\Controllers;

use App\Helpers\AuditHelper;
use App\Mail\AdminBroadcastMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AdminEmailController extends Controller
{
    private const MAX_RECIPIENTS_PER_SEND = 200;

    public const TYPE_GENERAL = 'general';
    public const TYPE_RESIDENCY = 'residency_expiry';
    public const TYPE_PASSPORT = 'passport_expiry';

    public function index(Request $request)
    {
        $this->authorizeAdmin($request);

        $users = User::query()
            ->where('approval_status', 'approved')
            ->where('is_active', true)
            ->whereNotNull('email')
            ->with('employee:id,user_id,name,residency_expiry_date,passport_expiry_date')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);

        return view('admin-emails.index', compact('users'));
    }

    public function send(Request $request)
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'message_type' => ['required', 'in:'.self::TYPE_GENERAL.','.self::TYPE_RESIDENCY.','.self::TYPE_PASSPORT],
            'title' => ['nullable', 'string', 'max:255', 'required_if:message_type,'.self::TYPE_GENERAL],
            'description' => ['nullable', 'string', 'max:10000', 'required_if:message_type,'.self::TYPE_GENERAL],
            'attachment' => [
                'nullable',
                'file',
                'max:25600', // 25MB
                'mimes:jpg,jpeg,png,gif,webp,mp4,mov,webm',
                'mimetypes:image/jpeg,image/png,image/gif,image/webp,video/mp4,video/quicktime,video/webm',
            ],
            'recipients' => ['required', 'array', 'min:1', 'max:'.self::MAX_RECIPIENTS_PER_SEND],
            'recipients.*' => ['integer', 'distinct', 'exists:users,id'],
        ], [], [
            'title' => __('admin_emails.field_title'),
            'description' => __('admin_emails.field_description'),
            'attachment' => __('admin_emails.field_attachment'),
            'recipients' => __('admin_emails.field_recipients'),
        ]);

        $type = $validated['message_type'];

        // Re-resolve recipients server-side; only approved & active users with a valid
        // account email are ever contacted, regardless of what IDs were submitted.
        $recipients = User::query()
            ->whereIn('id', $validated['recipients'])
            ->where('approval_status', 'approved')
            ->where('is_active', true)
            ->whereNotNull('email')
            ->with('employee:id,user_id,name,residency_expiry_date,passport_expiry_date')
            ->get(['id', 'name', 'email'])
            ->filter(fn (User $u) => filter_var($u->email, FILTER_VALIDATE_EMAIL) !== false);

        if ($recipients->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['recipients' => __('admin_emails.no_valid_recipients')]);
        }

        $attachmentPath = null;
        $attachmentName = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            // Private disk + server-generated hashed name; never publicly reachable.
            $attachmentPath = $file->store('admin-emails/tmp', 'local');
            $extension = strtolower($file->getClientOriginalExtension());
            $attachmentName = 'attachment'.($extension !== '' ? '.'.$extension : '');
        }

        $sent = 0;
        $failed = 0;
        $skipped = 0;

        try {
            foreach ($recipients as $recipient) {
                $mailable = $this->buildMailable($type, $validated, $recipient, $attachmentPath, $attachmentName);

                if ($mailable === null) {
                    $skipped++;
                    continue;
                }

                try {
                    Mail::to($recipient->email)->send($mailable);
                    $sent++;
                } catch (\Throwable $e) {
                    $failed++;
                    Log::warning('Admin broadcast email failed', [
                        'recipient_user_id' => $recipient->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } finally {
            if ($attachmentPath) {
                Storage::disk('local')->delete($attachmentPath);
            }
        }

        AuditHelper::log(
            'email_broadcast',
            'User',
            null,
            'type='.$type
                .' | title='.($validated['title'] ?? '-')
                .' | recipients='.$recipients->count()
                .' | sent='.$sent.' | failed='.$failed.' | skipped='.$skipped
        );

        if ($sent === 0) {
            $error = $skipped > 0 && $failed === 0
                ? __('admin_emails.send_all_skipped')
                : __('admin_emails.send_all_failed');

            return back()->withInput()->withErrors(['recipients' => $error]);
        }

        $message = __('admin_emails.send_success', ['count' => $sent]);
        if ($failed > 0) {
            $message .= ' '.__('admin_emails.send_partial_failed', ['count' => $failed]);
        }
        if ($skipped > 0) {
            $message .= ' '.__('admin_emails.send_skipped', ['count' => $skipped]);
        }

        return redirect()->route('admin-emails.index')->with('success', $message);
    }

    /**
     * Returns null when the recipient lacks the data required by the chosen template
     * (no linked employee or no expiry date), so the caller can skip them safely.
     */
    private function buildMailable(
        string $type,
        array $validated,
        User $recipient,
        ?string $attachmentPath,
        ?string $attachmentName,
    ): ?AdminBroadcastMail {
        if ($type === self::TYPE_GENERAL) {
            return new AdminBroadcastMail(
                $validated['title'],
                $validated['description'],
                null,
                '#2563eb',
                $attachmentPath,
                $attachmentName,
            );
        }

        $employee = $recipient->employee;
        $dateField = $type === self::TYPE_RESIDENCY ? 'residency_expiry_date' : 'passport_expiry_date';

        if (! $employee || ! $employee->{$dateField}) {
            return null;
        }

        $name = $employee->name ?: $recipient->name;
        $date = $employee->{$dateField}->format('Y-m-d');

        $subject = $type === self::TYPE_RESIDENCY
            ? __('admin_emails.tpl_residency_subject')
            : __('admin_emails.tpl_passport_subject');

        $body = $type === self::TYPE_RESIDENCY
            ? __('admin_emails.tpl_residency_body', ['name' => $name, 'date' => $date])
            : __('admin_emails.tpl_passport_body', ['name' => $name, 'date' => $date]);

        // Optional extra note written by the admin is appended below the template text.
        if (! empty($validated['description'])) {
            $body .= "\n\n".$validated['description'];
        }

        $details = [
            __('admin_emails.detail_employee_name') => $name,
            $type === self::TYPE_RESIDENCY
                ? __('admin_emails.detail_residency_expiry')
                : __('admin_emails.detail_passport_expiry') => $date,
        ];

        return new AdminBroadcastMail(
            $subject,
            $body,
            $details,
            '#dc2626',
            $attachmentPath,
            $attachmentName,
        );
    }

    private function authorizeAdmin(Request $request): void
    {
        $user = $request->user();

        if (! $user || ! $user->isAdminLike() || ! $user->isApprovedAndActive()) {
            abort(403, 'غير مصرح لك بالدخول');
        }
    }
}
