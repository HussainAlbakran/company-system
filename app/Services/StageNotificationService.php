<?php

namespace App\Services;

use App\Models\Project;
use App\Models\SalesContract;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StageNotificationService
{
    public function sendDesignStageNotification(SalesContract $contract): void
    {
        $recipient = $this->firstActiveUserByRole('engineer');

        if (! $recipient || ! $recipient->email) {
            Log::info('Design stage email skipped: no design manager email found.', [
                'contract_id' => $contract->id,
                'project_id' => $contract->project_id,
            ]);
            return;
        }

        Mail::send('emails.project_to_design', [
            'contract' => $contract,
        ], function ($message) use ($recipient): void {
            $message->to($recipient->email)
                ->subject('📌 مشروع جديد وصل إلى قسم التصاميم');
        });
    }

    public function sendFactoryStageNotification(Project $project): void
    {
        $recipient = $this->firstActiveUserByRole('factory_manager');
        $this->sendOperationalProjectMail($recipient?->email, 'emails.project_to_factory', '📌 مشروع جديد وصل من التصاميم', $project);
    }

    public function sendInstallationStageNotification(Project $project): void
    {
        $recipient = $this->firstActiveUserByRole('manager');
        $this->sendOperationalProjectMail($recipient?->email, 'emails.project_to_installation', '📌 مشروع جاهز للتركيب', $project);
    }

    public function sendPurchasesStageNotification(Project $project): void
    {
        $recipient = $this->firstActiveUserByRole('manager');
        $this->sendOperationalProjectMail($recipient?->email, 'emails.project_to_purchases', '📌 مشروع يحتاج مشتريات', $project);
    }

    private function firstActiveUserByRole(string $role): ?User
    {
        return User::where('role', $role)
            ->where('is_active', true)
            ->whereNotNull('email')
            ->orderBy('id')
            ->first();
    }

    private function sendOperationalProjectMail(?string $email, string $view, string $subject, Project $project): void
    {
        if (empty($email)) {
            Log::info('Operational stage email skipped: missing manager email.', [
                'project_id' => $project->id,
                'view' => $view,
            ]);
            return;
        }

        Mail::send($view, [
            'project' => $project,
        ], function ($message) use ($email, $subject): void {
            $message->to($email)->subject($subject);
        });
    }
}
