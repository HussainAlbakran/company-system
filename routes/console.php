<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schedule;
use App\Models\Employee;
use App\Models\User;
use App\Models\ResidencyAlertLog;
use Carbon\Carbon;
use App\Notifications\ResidencyExpiryNotification;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('check:residency-expiry', function () {
    $today = Carbon::today();

    $employees = Employee::with('department')->whereNotNull('residency_expiry_date')->get();

    foreach ($employees as $employee) {
        try {
            $expiryDate = Carbon::parse($employee->residency_expiry_date);
            $days = $today->diffInDays($expiryDate, false);

            // الإرسال فقط عند 30 / 15 / 7 / 1 / 0
            if (!in_array($days, [30, 15, 7, 1, 0])) {
                continue;
            }

            // منع التكرار في نفس اليوم
            $alreadySentToday = ResidencyAlertLog::where('employee_id', $employee->id)
                ->where('days_remaining', $days)
                ->whereDate('sent_date', $today)
                ->where('alert_type', 'email')
                ->exists();

            if ($alreadySentToday) {
                $this->info('تم تجاوز الموظف ' . $employee->name . ' لأن التنبيه أُرسل اليوم مسبقًا.');
                continue;
            }

            // إرسال للأدمن و HR
            $users = User::whereIn('role', ['admin', 'hr'])->get();

            foreach ($users as $user) {
                $user->notify(new ResidencyExpiryNotification($employee, $days));
            }

            // إرسال للموظف نفسه
            if (!empty($employee->user_id)) {
                $employeeUser = User::find($employee->user_id);

                if ($employeeUser) {
                    $employeeUser->notify(new ResidencyExpiryNotification($employee, $days));
                }
            } elseif (!empty($employee->email)) {
                Notification::route('mail', $employee->email)
                    ->notify(new ResidencyExpiryNotification($employee, $days));
            }

            ResidencyAlertLog::create([
                'employee_id' => $employee->id,
                'days_remaining' => $days,
                'sent_date' => $today->toDateString(),
                'alert_type' => 'email',
            ]);

            $this->info('تم إرسال تنبيه الإقامة للموظف: ' . $employee->name . ' | المتبقي: ' . $days . ' يوم');
        } catch (\Exception $e) {
            $this->error('خطأ في الموظف: ' . ($employee->name ?? 'غير معروف') . ' | ' . $e->getMessage());
        }
    }
})->purpose('Check residency expiry dates and send email alerts')->daily();

Schedule::command('alerts:send-residency-now')
    ->dailyAt('08:00')
    ->withoutOverlapping();

Schedule::command('alerts:send-passport-now')
    ->dailyAt('08:05')
    ->withoutOverlapping();