<?php

namespace App\Console\Commands;

use App\Services\EmployeeDocumentExpiryReminderService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendEmployeeDocumentExpiryReminders extends Command
{
    protected $signature = 'alerts:send-employee-document-expiry-reminders
                            {--date= : Date in Y-m-d format. Defaults to today.}
                            {--type=all : residency|passport|all}';

    protected $description = 'Dispatch queued email reminders for employee iqama/passport expiry windows.';

    public function handle(EmployeeDocumentExpiryReminderService $service): int
    {
        $type = strtolower((string) $this->option('type'));
        if (! in_array($type, ['all', 'residency', 'passport'], true)) {
            $this->error('Invalid --type value. Allowed: residency, passport, all.');
            return self::FAILURE;
        }

        $dateOption = (string) ($this->option('date') ?? '');

        try {
            $targetDate = $dateOption !== '' ? Carbon::parse($dateOption)->startOfDay() : now()->startOfDay();
        } catch (\Throwable $exception) {
            $this->error('Invalid --date value. Use Y-m-d format.');
            return self::FAILURE;
        }

        $stats = $service->dispatchForDate($targetDate, $type);

        $this->info("Expiry reminders dispatched for {$stats['target_date']}");
        $this->line('Types: ' . $stats['types']);
        $this->line('Windows: ' . implode(', ', $stats['windows']));
        $this->line('Employees checked: ' . $stats['employees_checked']);
        $this->line('Jobs dispatched: ' . $stats['jobs_dispatched']);
        $this->line('Skipped (already sent): ' . $stats['skipped_already_sent']);
        $this->line('Skipped (no email): ' . $stats['skipped_no_email']);

        return self::SUCCESS;
    }
}
