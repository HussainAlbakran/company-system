<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResidencyExpiryNotification extends Notification
{
    use Queueable;

    protected $employee;
    protected $days;

    public function __construct($employee, $days)
    {
        $this->employee = $employee;
        $this->days = $days;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        if ($this->days === 30) {
            $status = 'تنبيه: الإقامة تنتهي خلال 30 يوم';
            $message = 'إقامة الموظف ستنتهي خلال 30 يوم.';
        } elseif ($this->days === 15) {
            $status = 'تنبيه: الإقامة تنتهي خلال 15 يوم';
            $message = 'إقامة الموظف ستنتهي خلال 15 يوم.';
        } elseif ($this->days === 7) {
            $status = 'تنبيه عاجل: الإقامة تنتهي خلال 7 أيام';
            $message = 'إقامة الموظف ستنتهي خلال 7 أيام.';
        } elseif ($this->days === 1) {
            $status = 'تنبيه عاجل جدًا: الإقامة تنتهي غدًا';
            $message = 'إقامة الموظف ستنتهي خلال يوم واحد.';
        } elseif ($this->days === 0) {
            $status = 'تنبيه عاجل: الإقامة تنتهي اليوم';
            $message = 'إقامة الموظف تنتهي اليوم، يرجى اتخاذ الإجراء فورًا.';
        } else {
            $status = 'تنبيه انتهاء الإقامة';
            $message = 'هناك تحديث متعلق بتاريخ انتهاء الإقامة.';
        }

        return (new MailMessage)
            ->subject('تنبيه انتهاء الإقامة - ' . $this->employee->name)
            ->greeting('السلام عليكم ورحمة الله وبركاته')
            ->line('الموظف: ' . $this->employee->name)
            ->line('الرقم الوظيفي: ' . ($this->employee->employee_number ?? '-'))
            ->line('القسم: ' . ($this->employee->department->name ?? '-'))
            ->line('تاريخ انتهاء الإقامة: ' . ($this->employee->residency_expiry_date ?? '-'))
            ->line($status)
            ->line($message)
            ->line('يرجى مراجعة الادارة واتخاذ الإجراء المناسب.');
    }
}