<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ExpiryAlertService
{
    public function sendResidencyAlertsForDate(Carbon $targetDate, bool $isManual = false): array
    {
        $targetDateString = $targetDate->toDateString();
        $employees = Employee::query()
            ->whereNotNull('residency_expiry_date')
            ->whereDate('residency_expiry_date', '=', $targetDateString)
            ->get();

        $hrEmails = User::query()
            ->where('role', 'hr')
            ->whereNotNull('email')
            ->pluck('email')
            ->filter()
            ->unique()
            ->values();

        Log::info('Residency alerts started.', [
            'target_date' => $targetDateString,
            'is_manual' => $isManual,
            'employees_found' => $employees->count(),
            'hr_recipients_count' => $hrEmails->count(),
        ]);

        $stats = [
            'type' => 'residency',
            'target_date' => $targetDateString,
            'employees_found' => $employees->count(),
            'success_count' => 0,
            'failure_count' => 0,
            'recipients' => [],
            'errors' => [],
        ];

        foreach ($employees as $employee) {
            $employeeRecipients = collect();

            if (!empty($employee->email)) {
                $employeeRecipients->push($employee->email);
            }

            $allRecipients = $employeeRecipients->merge($hrEmails)->filter()->unique()->values();

            if ($allRecipients->isEmpty()) {
                Log::info('Residency alert skipped, no recipients.', [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name,
                ]);
                continue;
            }

            foreach ($allRecipients as $recipient) {
                $sent = $this->sendResidencyMailToRecipient($employee, $recipient);

                if ($sent) {
                    $stats['success_count']++;
                    $stats['recipients'][] = $recipient;
                    continue;
                }

                $stats['failure_count']++;
                $stats['errors'][] = "Failed sending residency alert to {$recipient} for employee #{$employee->id}";
            }
        }

        $stats['recipients'] = array_values(array_unique($stats['recipients']));

        Log::info('Residency alerts completed.', $stats);

        return $stats;
    }

    public function sendPassportAlertsForDate(Carbon $targetDate, bool $isManual = false): array
    {
        $targetDateString = $targetDate->toDateString();
        $employees = Employee::query()
            ->whereNotNull('passport_expiry_date')
            ->whereDate('passport_expiry_date', '=', $targetDateString)
            ->get();

        Log::info('Passport alerts started.', [
            'target_date' => $targetDateString,
            'is_manual' => $isManual,
            'employees_found' => $employees->count(),
        ]);

        $stats = [
            'type' => 'passport',
            'target_date' => $targetDateString,
            'employees_found' => $employees->count(),
            'success_count' => 0,
            'failure_count' => 0,
            'recipients' => [],
            'errors' => [],
        ];

        foreach ($employees as $employee) {
            if (empty($employee->email)) {
                Log::info('Passport alert skipped, employee email missing.', [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name,
                ]);
                continue;
            }

            $sent = $this->sendPassportMailToRecipient($employee, $employee->email);

            if ($sent) {
                $stats['success_count']++;
                $stats['recipients'][] = $employee->email;
                continue;
            }

            $stats['failure_count']++;
            $stats['errors'][] = "Failed sending passport alert to {$employee->email} for employee #{$employee->id}";
        }

        $stats['recipients'] = array_values(array_unique($stats['recipients']));

        Log::info('Passport alerts completed.', $stats);

        return $stats;
    }

    private function sendResidencyMailToRecipient(Employee $employee, string $recipient): bool
    {
        try {
            Mail::send('emails.residency_expiry_alert', [
                'employee' => $employee,
                'messageText' => 'تنبيه: تاريخ انتهاء الإقامة هو اليوم. يرجى اتخاذ الإجراء اللازم فورًا.',
            ], function ($message) use ($recipient): void {
                $message->to($recipient)->subject('تنبيه انتهاء الإقامة');
            });

            Log::info('Residency alert sent.', [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'recipient' => $recipient,
            ]);

            return true;
        } catch (Throwable $exception) {
            Log::error('Residency alert send failed.', [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'recipient' => $recipient,
                'error_message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function sendPassportMailToRecipient(Employee $employee, string $recipient): bool
    {
        try {
            Mail::send('emails.passport_expiry_alert', [
                'employee' => $employee,
                'messageText' => 'تنبيه: تاريخ انتهاء الجواز هو اليوم. يرجى تجديد الجواز فورًا.',
            ], function ($message) use ($recipient): void {
                $message->to($recipient)->subject('تنبيه انتهاء الجواز');
            });

            Log::info('Passport alert sent.', [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'recipient' => $recipient,
            ]);

            return true;
        } catch (Throwable $exception) {
            Log::error('Passport alert send failed.', [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'recipient' => $recipient,
                'error_message' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
