<?php

namespace App\Services;

use App\Jobs\SendEmployeeDocumentExpiryReminderJob;
use App\Models\Department;
use App\Models\Employee;
use App\Models\ResidencyAlertLog;
use App\Models\User;
use Carbon\Carbon;

class EmployeeDocumentExpiryReminderService
{
    public function __construct(
        private readonly InternalNotificationService $internalNotificationService
    ) {
    }

    /**
     * @param  string  $type  residency|passport|all
     */
    public function dispatchForDate(Carbon $targetDate, string $type = 'all'): array
    {
        $targetDate = $targetDate->copy()->startOfDay();
        $windows = $this->windows();

        $stats = [
            'target_date' => $targetDate->toDateString(),
            'windows' => $windows,
            'employees_checked' => 0,
            'jobs_dispatched' => 0,
            'skipped_already_sent' => 0,
            'skipped_no_email' => 0,
            'types' => $type,
        ];

        $hrManagerEmail = $type !== 'passport' ? $this->resolveHrManagerEmail() : null;

        $employees = Employee::query()
            ->where(function ($query) use ($type) {
                if ($type === 'residency') {
                    $query->whereNotNull('residency_expiry_date');

                    return;
                }

                if ($type === 'passport') {
                    $query->whereNotNull('passport_expiry_date');

                    return;
                }

                $query->whereNotNull('residency_expiry_date')
                    ->orWhereNotNull('passport_expiry_date');
            })
            ->get();

        $stats['employees_checked'] = $employees->count();

        foreach ($employees as $employee) {
            if ($type !== 'passport' && $employee->residency_expiry_date) {
                $days = $targetDate->diffInDays(Carbon::parse($employee->residency_expiry_date)->startOfDay(), false);
                if (in_array($days, $windows, true)) {
                    $hr = $hrManagerEmail !== null && $hrManagerEmail !== '' ? trim((string) $hrManagerEmail) : '';
                    $emp = trim((string) optional($employee->user)->email);

                    $sentHr = $hr !== '' && $this->tryDispatchReminder(
                        $employee,
                        $hr,
                        'residency',
                        $days,
                        $targetDate,
                        'residency_email_hr',
                        $stats
                    );

                    $sentEmp = false;
                    if ($emp !== '' && ($hr === '' || strcasecmp($emp, $hr) !== 0)) {
                        $sentEmp = $this->tryDispatchReminder(
                            $employee,
                            $emp,
                            'residency',
                            $days,
                            $targetDate,
                            'residency_email_employee',
                            $stats
                        );
                    }

                    if ($hr === '' && $emp === '') {
                        $stats['skipped_no_email']++;
                    }

                    if ($sentHr || $sentEmp) {
                        $this->internalNotificationService->notifyEmployeeDocumentExpiry(
                            $employee,
                            'residency',
                            $days,
                            $targetDate
                        );
                    }
                }
            }

            if ($type !== 'residency' && $employee->passport_expiry_date) {
                $emp = trim((string) optional($employee->user)->email);
                if ($emp === '') {
                    $stats['skipped_no_email']++;
                } else {
                    $days = $targetDate->diffInDays(Carbon::parse($employee->passport_expiry_date)->startOfDay(), false);
                    if (in_array($days, $windows, true)) {
                        $sent = $this->tryDispatchReminder(
                            $employee,
                            $emp,
                            'passport',
                            $days,
                            $targetDate,
                            'passport_email',
                            $stats
                        );

                        if ($sent) {
                            $this->internalNotificationService->notifyEmployeeDocumentExpiry(
                                $employee,
                                'passport',
                                $days,
                                $targetDate
                            );
                        }
                    }
                }
            }
        }

        return $stats;
    }

    public function handle(): void
    {
        $this->dispatchForDate(Carbon::today(), 'all');
    }

    /**
     * @return  bool  True if a new reminder job was queued for this recipient/channel.
     */
    private function tryDispatchReminder(
        Employee $employee,
        string $recipient,
        string $documentType,
        int $days,
        Carbon $targetDate,
        string $logAlertType,
        array &$stats
    ): bool {
        if ($recipient === '') {
            return false;
        }

        if ($this->alreadySent($employee->id, $days, $targetDate, $logAlertType)) {
            $stats['skipped_already_sent']++;

            return false;
        }

        SendEmployeeDocumentExpiryReminderJob::dispatch(
            $employee->id,
            $recipient,
            $documentType,
            $days,
            $targetDate->toDateString(),
            $logAlertType
        );
        $stats['jobs_dispatched']++;

        return true;
    }

    private function windows(): array
    {
        $windows = config('expiry_reminders.windows', [90, 60, 30]);
        $windows = array_values(array_unique(array_map(static fn ($day) => (int) $day, $windows)));
        sort($windows);

        return $windows;
    }

    private function alreadySent(int $employeeId, int $daysRemaining, Carbon $targetDate, string $alertType): bool
    {
        return ResidencyAlertLog::query()
            ->where('employee_id', $employeeId)
            ->where('days_remaining', $daysRemaining)
            ->whereDate('sent_date', $targetDate->toDateString())
            ->where('alert_type', $alertType)
            ->exists();
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
