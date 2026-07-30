<?php

namespace App\Console\Commands;

use App\Services\WeeklyProjectReportAlertService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendWeeklyProjectReportAlerts extends Command
{
    protected $signature = 'alerts:weekly-project-reports
                            {--date= : Date/time in app timezone (Y-m-d or Y-m-d H:i). Defaults to now.}
                            {--force : Resend emails even if in-app alert already exists}';

    protected $description = 'Alert admins when active projects are missing Thursday weekly reports (deadline 12:00).';

    public function handle(WeeklyProjectReportAlertService $service): int
    {
        $dateOption = $this->option('date');

        try {
            $now = $dateOption
                ? Carbon::parse($dateOption, config('app.timezone'))
                : now()->timezone(config('app.timezone'));
        } catch (\Throwable $e) {
            $this->error('Invalid --date. Use Y-m-d or Y-m-d H:i');

            return self::FAILURE;
        }

        $ctx = $service->weekContext($now);

        if (! $ctx['deadline_passed']) {
            $this->warn('Deadline not reached yet (Thursday 12:00). Nothing sent.');
            $this->line('Now: '.$ctx['now']->toDateTimeString());
            $this->line('Deadline: '.$ctx['deadline']->toDateTimeString());

            return self::SUCCESS;
        }

        $stats = $service->sendAlerts($now, (bool) $this->option('force'));

        $this->info('Weekly project report alerts completed.');
        $this->line('Week: '.$ctx['week_start']->toDateString().' → '.$ctx['week_end']->toDateString());
        $this->line('Thursday deadline: '.$ctx['deadline']->toDateTimeString());
        $this->line('Missing projects: '.$stats['missing_count']);
        $this->line('In-app notifications created: '.$stats['notified_users']);
        $this->line('Emails sent: '.$stats['emails_sent']);
        $this->line('Skipped (already notified): '.$stats['skipped']);

        foreach ($stats['errors'] as $error) {
            $this->error($error);
        }

        return self::SUCCESS;
    }
}
