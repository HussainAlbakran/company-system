<?php

namespace App\Notifications;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployeeDocumentExpiryReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Employee $employee,
        private readonly string $documentType,
        private readonly int $daysRemaining
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isResidency = $this->documentType === 'residency';
        $documentLabel = $isResidency ? 'الإقامة' : 'الجواز';
        $expiryDate = $isResidency
            ? (string) ($this->employee->residency_expiry_date ?? '-')
            : (string) ($this->employee->passport_expiry_date ?? '-');

        $timingLine = $this->daysRemaining === 0
            ? "تنبيه عاجل: {$documentLabel} تنتهي اليوم."
            : "تنبيه: {$documentLabel} تنتهي خلال {$this->daysRemaining} يوم.";

        return (new MailMessage)
            ->subject("تنبيه انتهاء {$documentLabel} - {$this->employee->name}")
            ->greeting('السلام عليكم ورحمة الله وبركاته')
            ->line("الموظف: {$this->employee->name}")
            ->line('الرقم الوظيفي: ' . ($this->employee->employee_number ?? '-'))
            ->line("تاريخ انتهاء {$documentLabel}: {$expiryDate}")
            ->line($timingLine)
            ->line('يرجى اتخاذ الإجراء اللازم في الوقت المناسب.');
    }
}
