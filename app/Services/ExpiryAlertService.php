<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ExpiryAlertService
{
    public function handle(): void
    {
        $today = Carbon::today();
        $this->sendVehicleRegistrationAlertsForDate($today, false);
        $this->sendVehicleInspectionAlertsForDate($today, false);
    }

    public function sendResidencyAlertsForDate(Carbon $targetDate, bool $isManual = false): array
    {
        $targetDateString = $targetDate->toDateString();

        $employees = Employee::query()
            ->whereNotNull('residency_expiry_date')
            ->whereDate('residency_expiry_date', '<=', $targetDateString)
            ->get();

        $hrManagerEmail = $this->resolveHrManagerEmail();

        Log::info('Residency alerts started.', [
            'target_date' => $targetDateString,
            'is_manual' => $isManual,
            'employees_found' => $employees->count(),
            'hr_recipients_count' => empty($hrManagerEmail) ? 0 : 1,
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
            $hr = $hrManagerEmail !== null && $hrManagerEmail !== '' ? trim((string) $hrManagerEmail) : '';
            $employeeEmail = trim((string) optional($employee->user)->email);

            if ($hr === '' && $employeeEmail === '') {
                Log::info('Residency alert skipped, no recipients.', [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name,
                ]);

                continue;
            }

            if ($hr !== '') {
                $sent = $this->sendResidencyMailToRecipient($employee, $hr);

                if ($sent) {
                    $stats['success_count']++;
                    $stats['recipients'][] = $hr;
                } else {
                    $stats['failure_count']++;
                    $stats['errors'][] = "Failed sending residency alert to {$hr} for employee #{$employee->id}";
                }
            }

            if ($employeeEmail !== '' && ($hr === '' || strcasecmp($employeeEmail, $hr) !== 0)) {
                $sent = $this->sendResidencyMailToRecipient($employee, $employeeEmail);

                if ($sent) {
                    $stats['success_count']++;
                    $stats['recipients'][] = $employeeEmail;
                } else {
                    $stats['failure_count']++;
                    $stats['errors'][] = "Failed sending residency alert to {$employeeEmail} for employee #{$employee->id}";
                }
            }
        }

        $stats['recipients'] = array_values(array_unique($stats['recipients']));

        Log::info('Residency alerts completed.', $stats);

        return $stats;
    }

    public function sendPassportAlertsForDate(Carbon $targetDate, bool $isManual = false): array
    {
        $targetDateString = $targetDate->toDateString();

        $windowEnd = $targetDate->copy()->addMonths(8)->toDateString();

        $employees = Employee::query()
            ->whereNotNull('passport_expiry_date')
            ->where(function ($q) use ($targetDateString, $windowEnd) {
                $q->whereDate('passport_expiry_date', '<', $targetDateString)
                    ->orWhereBetween('passport_expiry_date', [$targetDateString, $windowEnd]);
            })
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
            $recipient = trim((string) optional($employee->user)->email);
            if ($recipient === '') {
                Log::info('Passport alert skipped, employee email missing.', [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name,
                ]);
                continue;
            }

            $sent = $this->sendPassportMailToRecipient($employee, $recipient);

            if ($sent) {
                $stats['success_count']++;
                $stats['recipients'][] = $recipient;
            } else {
                $stats['failure_count']++;
                $stats['errors'][] = "Failed sending passport alert to {$recipient} for employee #{$employee->id}";
            }
        }

        $stats['recipients'] = array_values(array_unique($stats['recipients']));

        Log::info('Passport alerts completed.', $stats);

        return $stats;
    }

    public function sendVehicleRegistrationAlertsForDate(Carbon $targetDate, bool $isManual = false): array
    {
        $targetDateString = $targetDate->toDateString();
        $thresholdDate = $targetDate->copy()->addDays(60)->toDateString();

        $assets = Asset::query()
            ->whereIn('asset_type', ['vehicle', 'مركبة'])
            ->whereNotNull('registration_expiry_date')
            ->whereBetween('registration_expiry_date', [$targetDateString, $thresholdDate])
            ->orderBy('registration_expiry_date')
            ->get();

        $recipient = $this->resolveHrManagerEmail();

        $stats = [
            'type' => 'vehicle_registration',
            'target_date' => $targetDateString,
            'assets_found' => $assets->count(),
            'success_count' => 0,
            'failure_count' => 0,
            'recipients' => [],
            'errors' => [],
        ];

        if (empty($recipient) || $assets->isEmpty()) {
            return $stats;
        }

        foreach ($assets as $asset) {
            try {
                $expiryDate = (string) $asset->registration_expiry_date;
                $message = "تنبيه قرب انتهاء استمارة مركبة.\n"
                    . "الأصل: {$asset->name}\n"
                    . "الكمية: " . (string) ($asset->quantity ?? '-') . "\n"
                    . "الرقم التسلسلي: {$asset->serial_number}\n"
                    . "نوع المركبة: " . ($asset->vehicle_type ?? '-') . "\n"
                    . "اللون: " . ($asset->color ?? '-') . "\n"
                    . "رقم اللوحة: " . ($asset->plate_number ?? '-') . "\n"
                    . "رقم الاستمارة: " . ($asset->registration_number ?? '-') . "\n"
                    . "تاريخ انتهاء الاستمارة: {$expiryDate}";

                Mail::raw($message, function ($mail) use ($recipient): void {
                    $mail->to($recipient)->subject('تنبيه قرب انتهاء استمارة مركبة');
                });

                $stats['success_count']++;
                $stats['recipients'][] = $recipient;
            } catch (Throwable $exception) {
                $stats['failure_count']++;
                $stats['errors'][] = "Failed vehicle registration alert for asset #{$asset->id}";
                Log::error('Vehicle registration alert send failed.', [
                    'asset_id' => $asset->id,
                    'recipient' => $recipient,
                    'error_message' => $exception->getMessage(),
                ]);
            }
        }

        $stats['recipients'] = array_values(array_unique($stats['recipients']));
        return $stats;
    }

    public function sendVehicleInspectionAlertsForDate(Carbon $targetDate, bool $isManual = false): array
    {
        $targetDateString = $targetDate->toDateString();
        $thresholdDate = $targetDate->copy()->addDays(60)->toDateString();

        $assets = Asset::query()
            ->whereIn('asset_type', ['vehicle', 'مركبة'])
            ->whereNotNull('inspection_expiry_date')
            ->whereBetween('inspection_expiry_date', [$targetDateString, $thresholdDate])
            ->orderBy('inspection_expiry_date')
            ->get();

        $recipient = $this->resolveHrManagerEmail();

        $stats = [
            'type' => 'vehicle_inspection',
            'target_date' => $targetDateString,
            'assets_found' => $assets->count(),
            'success_count' => 0,
            'failure_count' => 0,
            'recipients' => [],
            'errors' => [],
        ];

        if (empty($recipient) || $assets->isEmpty()) {
            return $stats;
        }

        foreach ($assets as $asset) {
            try {
                $expiryDate = (string) $asset->inspection_expiry_date;
                $message = "تنبيه قرب انتهاء فحص مركبة.\n"
                    . "الأصل: {$asset->name}\n"
                    . "الكمية: " . (string) ($asset->quantity ?? '-') . "\n"
                    . "الرقم التسلسلي: {$asset->serial_number}\n"
                    . "نوع المركبة: " . ($asset->vehicle_type ?? '-') . "\n"
                    . "اللون: " . ($asset->color ?? '-') . "\n"
                    . "رقم اللوحة: " . ($asset->plate_number ?? '-') . "\n"
                    . "تاريخ انتهاء الفحص: {$expiryDate}";

                Mail::raw($message, function ($mail) use ($recipient): void {
                    $mail->to($recipient)->subject('تنبيه قرب انتهاء فحص مركبة');
                });

                $stats['success_count']++;
                $stats['recipients'][] = $recipient;
            } catch (Throwable $exception) {
                $stats['failure_count']++;
                $stats['errors'][] = "Failed vehicle inspection alert for asset #{$asset->id}";
                Log::error('Vehicle inspection alert send failed.', [
                    'asset_id' => $asset->id,
                    'recipient' => $recipient,
                    'error_message' => $exception->getMessage(),
                ]);
            }
        }

        $stats['recipients'] = array_values(array_unique($stats['recipients']));

        return $stats;
    }

    private function sendResidencyMailToRecipient(Employee $employee, string $recipient): bool
    {
        try {
            Mail::send('emails.residency_expiry_alert', [
                'employee' => $employee,
                'messageText' => 'تنبيه: تاريخ انتهاء الإقامة هو اليوم أو مضى. يرجى اتخاذ الإجراء اللازم فورًا.',
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
                'messageText' => 'تنبيه: تاريخ انتهاء الجواز هو اليوم أو مضى. يرجى تجديد الجواز فورًا.',
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

    private function resolveHrManagerEmail(): ?string
    {
        $aliases = ['الموارد البشرية', 'hr', 'human resources'];

        $department = Department::query()
            ->with('managerUser')
            ->where(function ($query) use ($aliases) {
                foreach ($aliases as $alias) {
                    $query->orWhereRaw('LOWER(name) = ?', [mb_strtolower($alias)]);
                }
            })
            ->orderBy('id')
            ->first();

        $manager = $department?->managerUser;
        if ($manager && $manager->is_active && $manager->approval_status === 'approved' && ! empty($manager->email)) {
            return trim((string) $manager->email);
        }

        $fallbackHrUser = User::query()
            ->where('role', 'hr')
            ->where('is_active', true)
            ->where('approval_status', 'approved')
            ->whereNotNull('email')
            ->orderBy('id')
            ->first();

        return $fallbackHrUser?->email ? trim((string) $fallbackHrUser->email) : null;
    }
}