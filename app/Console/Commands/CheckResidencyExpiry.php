<?php

namespace App\Console\Commands;

use App\Services\ExpiryAlertService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckResidencyExpiry extends Command
{
    protected $signature = 'check:residency-expiry';
    protected $description = 'Send residency alerts using unified ExpiryAlertService flow';

    public function handle(ExpiryAlertService $expiryAlertService): int
    {
        $targetDate = Carbon::today()->addDays(30);
        $stats = $expiryAlertService->sendResidencyAlertsForDate($targetDate, true);

        $this->info("Residency alerts completed for {$stats['target_date']}");
        $this->line("Employees found: {$stats['employees_found']}");
        $this->line("Sent: {$stats['success_count']}");
        $this->line("Failed: {$stats['failure_count']}");

        return self::SUCCESS;
    }
}