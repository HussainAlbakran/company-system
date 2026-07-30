<?php

namespace App\Services;

use App\Mail\AdminBroadcastMail;
use App\Models\InternalNotification;
use App\Models\Project;
use App\Models\ProjectReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WeeklyProjectReportAlertService
{
    /**
     * Current week window (Mon–Sun) and Thursday 12:00 deadline in app timezone.
     *
     * @return array{
     *     now: Carbon,
     *     week_start: Carbon,
     *     week_end: Carbon,
     *     thursday: Carbon,
     *     deadline: Carbon,
     *     deadline_passed: bool
     * }
     */
    public function weekContext(?Carbon $now = null): array
    {
        $now = ($now ?? now())->copy()->timezone(config('app.timezone'));
        $weekStart = $now->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $weekEnd = $now->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
        $thursday = $weekStart->copy()->addDays(3)->startOfDay();
        $deadline = $thursday->copy()->setTime(12, 0, 0);

        return [
            'now' => $now,
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
            'thursday' => $thursday,
            'deadline' => $deadline,
            'deadline_passed' => $now->greaterThanOrEqualTo($deadline),
        ];
    }

    public function projectHasWeeklyReport(Project $project, Carbon $weekStart, Carbon $weekEnd): bool
    {
        return ProjectReport::query()
            ->where('project_id', $project->id)
            ->where('report_type', ProjectReport::TYPE_WEEKLY)
            ->whereDate('report_date', '>=', $weekStart->toDateString())
            ->whereDate('report_date', '<=', $weekEnd->toDateString())
            ->exists();
    }

    /**
     * Active projects missing this week's weekly report (after Thursday 12:00).
     *
     * @return Collection<int, Project>
     */
    public function missingProjects(?Carbon $now = null): Collection
    {
        $ctx = $this->weekContext($now);

        if (! $ctx['deadline_passed']) {
            return collect();
        }

        return Project::query()
            ->active()
            ->orderBy('name')
            ->get()
            ->filter(fn (Project $project) => ! $this->projectHasWeeklyReport(
                $project,
                $ctx['week_start'],
                $ctx['week_end']
            ))
            ->values();
    }

    /**
     * Recipients: الإدارة العليا + مدير النظام.
     *
     * @return Collection<int, User>
     */
    public function recipients(): Collection
    {
        return User::query()
            ->whereIn('role', [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN])
            ->where('is_active', true)
            ->where('approval_status', 'approved')
            ->orderBy('id')
            ->get();
    }

    /**
     * Send in-app + email alerts for missing weekly reports (idempotent per Thursday).
     *
     * @return array{missing_count: int, notified_users: int, emails_sent: int, skipped: int, errors: array<int, string>}
     */
    public function sendAlerts(?Carbon $now = null, bool $forceEmail = false): array
    {
        $ctx = $this->weekContext($now);
        $missing = $this->missingProjects($now);
        $recipients = $this->recipients();
        $eventDate = $ctx['thursday']->toDateString();

        $stats = [
            'missing_count' => $missing->count(),
            'notified_users' => 0,
            'emails_sent' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        if ($missing->isEmpty() || $recipients->isEmpty()) {
            return $stats;
        }

        foreach ($missing as $project) {
            $title = 'تنبيه: تقرير أسبوعي ناقص';
            $code = $project->project_code ?: '#'.$project->id;
            $message = 'المشروع '.$project->name.' ('.$code.') لم يتم تسجيل تقرير أسبوعي.';

            foreach ($recipients as $user) {
                $exists = InternalNotification::query()
                    ->where('user_id', $user->id)
                    ->where('type', 'weekly_project_report_missing')
                    ->where('reference_type', 'project')
                    ->where('reference_id', $project->id)
                    ->whereDate('event_date', $eventDate)
                    ->exists();

                if ($exists && ! $forceEmail) {
                    $stats['skipped']++;
                    continue;
                }

                if (! $exists) {
                    InternalNotification::create([
                        'user_id' => $user->id,
                        'type' => 'weekly_project_report_missing',
                        'title' => $title,
                        'message' => $message,
                        'reference_type' => 'project',
                        'reference_id' => $project->id,
                        'event_date' => $eventDate,
                    ]);
                    $stats['notified_users']++;
                }

                if (! filled($user->email)) {
                    continue;
                }

                try {
                    Mail::to($user->email)->send(new AdminBroadcastMail(
                        emailTitle: $title,
                        emailBody: $message."\n\nآخر مهلة لتسجيل التقرير الأسبوعي: يوم الخميس الساعة 12:00 ظهراً.\nيرجى تسجيل التقرير من صفحة تقارير المشاريع.",
                        details: [
                            'المشروع' => $project->name,
                            'كود المشروع' => $code,
                            'تاريخ الخميس' => $eventDate,
                            'المهلة' => '12:00 ظهراً',
                        ],
                        accentColor: '#dc2626',
                    ));
                    $stats['emails_sent']++;
                } catch (\Throwable $e) {
                    report($e);
                    Log::warning('weekly_project_report_alert_mail_failed', [
                        'user_id' => $user->id,
                        'project_id' => $project->id,
                        'error' => $e->getMessage(),
                    ]);
                    $stats['errors'][] = 'user#'.$user->id.' project#'.$project->id.': '.$e->getMessage();
                }
            }
        }

        return $stats;
    }
}
