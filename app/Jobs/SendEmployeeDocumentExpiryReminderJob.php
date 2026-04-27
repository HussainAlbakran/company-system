<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\ResidencyAlertLog;
use App\Notifications\EmployeeDocumentExpiryReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class SendEmployeeDocumentExpiryReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $employeeId,
        private readonly string $recipientEmail,
        private readonly string $documentType,
        private readonly int $daysRemaining,
        private readonly string $targetDate,
        private readonly ?string $logAlertType = null
    ) {
    }

    public function handle(): void
    {
        $employee = Employee::find($this->employeeId);
        if (! $employee) {
            return;
        }

        $alertType = $this->logAlertType ?? "{$this->documentType}_email";
        $alreadySent = ResidencyAlertLog::query()
            ->where('employee_id', $employee->id)
            ->where('days_remaining', $this->daysRemaining)
            ->whereDate('sent_date', $this->targetDate)
            ->where('alert_type', $alertType)
            ->exists();

        if ($alreadySent) {
            return;
        }

        try {
            Notification::route('mail', $this->recipientEmail)
                ->notify(new EmployeeDocumentExpiryReminderNotification(
                    $employee,
                    $this->documentType,
                    $this->daysRemaining
                ));

            ResidencyAlertLog::create([
                'employee_id' => $employee->id,
                'days_remaining' => $this->daysRemaining,
                'sent_date' => $this->targetDate,
                'alert_type' => $alertType,
            ]);
        } catch (Throwable $exception) {
            Log::error('Employee expiry reminder send failed.', [
                'employee_id' => $employee->id,
                'document_type' => $this->documentType,
                'days_remaining' => $this->daysRemaining,
                'target_date' => $this->targetDate,
                'recipient' => $this->recipientEmail,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
