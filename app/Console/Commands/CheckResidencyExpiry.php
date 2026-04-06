<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CheckResidencyExpiry extends Command
{
    protected $signature = 'check:residency-expiry';
    protected $description = 'Check employees residency expiry and send email before 30 days';

    public function handle()
    {
        $today = Carbon::today();
        $targetDate = $today->copy()->addDays(30);

        $employees = Employee::whereDate('residency_expiry_date', $targetDate)->get();

        foreach ($employees as $employee) {

            Mail::raw(
                "تنبيه: إقامة الموظف {$employee->name} ستنتهي بتاريخ {$employee->residency_expiry_date}",
                function ($message) {
                    $message->to('altaqaddum.system@gmail.com')
                            ->subject('تنبيه انتهاء إقامة');
                }
            );

        }

        $this->info('تم فحص الإقامات بنجاح');
    }
}