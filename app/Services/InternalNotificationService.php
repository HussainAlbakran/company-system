<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\InternalNotification;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;

class InternalNotificationService
{
    public function notifyEmployeeDocumentExpiry(Employee $employee, string $documentType, int $daysRemaining, Carbon $targetDate): void
    {
        if ($documentType === 'passport') {
            // الجواز: لا إشعارات داخلية لـ HR/الإدارة — التنبيه للموظف عبر البريد فقط.
            return;
        }

        $recipients = User::query()
            ->whereIn('role', ['admin', 'hr'])
            ->where('is_active', true)
            ->where('approval_status', 'approved')
            ->get(['id']);

        $documentLabel = $documentType === 'passport' ? 'الجواز' : 'الإقامة';
        $timing = $daysRemaining === 0
            ? "{$documentLabel} تنتهي اليوم"
            : "{$documentLabel} تنتهي خلال {$daysRemaining} يوم";

        foreach ($recipients as $recipient) {
            $exists = InternalNotification::query()
                ->where('user_id', $recipient->id)
                ->where('type', "employee_{$documentType}_expiry")
                ->where('reference_type', 'employee')
                ->where('reference_id', $employee->id)
                ->whereDate('event_date', $targetDate->toDateString())
                ->exists();

            if ($exists) {
                continue;
            }

            InternalNotification::create([
                'user_id' => $recipient->id,
                'type' => "employee_{$documentType}_expiry",
                'title' => "تنبيه انتهاء {$documentLabel}",
                'message' => "الموظف {$employee->name}: {$timing}",
                'reference_type' => 'employee',
                'reference_id' => $employee->id,
                'event_date' => $targetDate->toDateString(),
            ]);
        }
    }

    public function notifyDepartmentStageArrival(Project $project, string $toStage, ?int $managerUserId): void
    {
        if (! $managerUserId) {
            return;
        }

        $stageLabels = [
            'architect' => 'التصاميم',
            'purchasing' => 'المشتريات',
            'production_installation' => 'المصنع/التركيب',
            'installation' => 'التركيبات',
        ];

        $stageLabel = $stageLabels[$toStage] ?? $toStage;

        $exists = InternalNotification::query()
            ->where('user_id', $managerUserId)
            ->where('type', 'stage_arrival')
            ->where('reference_type', 'project')
            ->where('reference_id', $project->id)
            ->whereDate('event_date', now()->toDateString())
            ->where('message', 'like', "%{$stageLabel}%")
            ->exists();

        if ($exists) {
            return;
        }

        InternalNotification::create([
            'user_id' => $managerUserId,
            'type' => 'stage_arrival',
            'title' => 'وصول مشروع إلى قسمك',
            'message' => "وصل المشروع {$project->name} إلى مرحلة {$stageLabel}.",
            'reference_type' => 'project',
            'reference_id' => $project->id,
            'event_date' => now()->toDateString(),
        ]);
    }
}
