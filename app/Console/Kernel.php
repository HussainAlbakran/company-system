<?php

namespace App\Console;

use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $today = Carbon::today();
            $leaves = Leave::where('status', 'approved')
                ->whereDate('start_date', '<=', $today)
                ->where('is_deducted', false)
                ->get();

            foreach ($leaves as $leave) {
                $employee = $leave->employee;
                if ($employee && $employee->leave_balance >= $leave->days) {
                    $employee->leave_balance -= $leave->days;
                    $employee->save();
                    $leave->is_deducted = true;
                    $leave->deducted_at = now();
                    $leave->save();
                }
            }
        })->daily();
        $schedule->command('alerts:send-residency-now')->dailyAt('08:00')->withoutOverlapping();
        $schedule->command('alerts:send-passport-now')->dailyAt('08:05')->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }
}