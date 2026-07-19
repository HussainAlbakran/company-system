<?php

namespace App\Console;

use App\Models\DismissedRequest;
use App\Models\Leave;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            try {
                app(\App\Services\EmployeeDocumentExpiryReminderService::class)->handle();
                app(\App\Services\ExpiryAlertService::class)->handle();
            } catch (\Throwable $e) {
                Log::error('Scheduler failed: '.$e->getMessage());
            }
        })->daily();

        // Legacy: approved before on-approval deduction (is_deducted = false).
        $schedule->call(function () {
            Leave::query()
                ->where('status', 'approved')
                ->where('is_deducted', false)
                ->with('employee')
                ->each(function (Leave $leave) {
                    $employee = $leave->employee;
                    if (! $employee || $employee->leave_balance < $leave->days) {
                        return;
                    }
                    $employee->decrement('leave_balance', (int) $leave->days);
                    $leave->update([
                        'is_deducted' => true,
                        'deducted_at' => now(),
                    ]);
                });
        })->daily();

        $schedule->call(function (): void {
            DismissedRequest::query()
                ->where('hidden_until', '<=', now())
                ->delete();
        })->hourly();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}