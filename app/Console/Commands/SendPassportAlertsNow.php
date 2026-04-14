<?php

namespace App\Console\Commands;

use App\Services\ExpiryAlertService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendPassportAlertsNow extends Command
{
    protected $signature = 'alerts:send-passport-now {--date= : Date in Y-m-d format. Defaults to today.}';

    protected $description = 'Send passport expiry alerts immediately.';

    public function handle(ExpiryAlertService $expiryAlertService): int
    {
        $dateOption = $this->option('date');
        try {
            $targetDate = $dateOption ? Carbon::parse($dateOption)->startOfDay() : now()->startOfDay();
        } catch (\Throwable $exception) {
            $this->error('Invalid --date value. Use Y-m-d format, for example: 2026-04-14');
            return self::FAILURE;
        }

        $stats = $expiryAlertService->sendPassportAlertsForDate($targetDate, true);

        $this->info("Passport alerts completed for {$stats['target_date']}");
        $this->line("Employees found: {$stats['employees_found']}");
        $this->line("Sent: {$stats['success_count']}");
        $this->line("Failed: {$stats['failure_count']}");
        $this->line('Recipients: ' . (empty($stats['recipients']) ? '-' : implode(', ', $stats['recipients'])));

        if (!empty($stats['errors'])) {
            foreach ($stats['errors'] as $error) {
                $this->error($error);
            }
        }

        return self::SUCCESS;
    }
}
