<?php

namespace App\Services;

use App\Mail\ProjectMovedToDesignMail;
use App\Mail\ProjectStageNotificationMail;
use App\Models\Department;
use App\Models\Project;
use App\Models\SalesContract;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class StageNotificationService
{
    public function __construct(
        private readonly InternalNotificationService $internalNotificationService
    ) {
    }

    public function sendDesignStageNotification(SalesContract $contract): void
    {
        [$recipientEmail, $managerUserId] = $this->destinationDepartmentManagerContact('architect');

        if (! $recipientEmail) {
            Log::info('Design stage email skipped: no design manager email found.', [
                'contract_id' => $contract->id,
                'project_id' => $contract->project_id,
            ]);
        } else {
            try {
                Mail::to($recipientEmail)->send(new ProjectMovedToDesignMail($contract));
            } catch (Throwable $exception) {
                Log::error('Design stage email failed.', [
                    'contract_id' => $contract->id,
                    'project_id' => $contract->project_id,
                    'recipient' => $recipientEmail,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        if ($contract->project) {
            $this->internalNotificationService->notifyDepartmentStageArrival(
                $contract->project,
                'architect',
                $managerUserId
            );
        }
    }

    public function sendFactoryStageNotification(Project $project): void
    {
        [$recipientEmail, $managerUserId] = $this->destinationDepartmentManagerContact('production_installation');
        $this->sendOperationalProjectMail(
            $recipientEmail,
            'emails.project_to_factory',
            'مشروع جديد وصل من التصاميم',
            $project
        );
        $this->internalNotificationService->notifyDepartmentStageArrival($project, 'production_installation', $managerUserId);
    }

    public function sendInstallationStageNotification(Project $project): void
    {
        [$recipientEmail, $managerUserId] = $this->destinationDepartmentManagerContact('installation');
        $this->sendOperationalProjectMail(
            $recipientEmail,
            'emails.project_to_installation',
            'مشروع جاهز للتركيب',
            $project
        );
        $this->internalNotificationService->notifyDepartmentStageArrival($project, 'installation', $managerUserId);
    }

    public function sendPurchasesStageNotification(Project $project): void
    {
        [$recipientEmail, $managerUserId] = $this->destinationDepartmentManagerContact('purchasing');
        $this->sendOperationalProjectMail(
            $recipientEmail,
            'emails.project_to_purchases',
            'مشروع يحتاج مشتريات',
            $project
        );
        $this->internalNotificationService->notifyDepartmentStageArrival($project, 'purchasing', $managerUserId);
    }

    public function sendStageTransferNotification(Project $project, string $toStage): void
    {
        if ($toStage === 'purchasing') {
            $this->sendPurchasesStageNotification($project);

            return;
        }

        if ($toStage === 'architect' && $project->salesContract) {
            $this->sendDesignStageNotification($project->salesContract);
        }
    }

    private function sendOperationalProjectMail(?string $email, string $view, string $subject, Project $project): void
    {
        if (empty($email)) {
            Log::info('Operational stage email skipped: missing manager email.', [
                'project_id' => $project->id,
                'view' => $view,
            ]);

            return;
        }

        try {
            Mail::to($email)->send(new ProjectStageNotificationMail($project, $view, $subject));
        } catch (Throwable $exception) {
            Log::error('Operational stage email failed.', [
                'project_id' => $project->id,
                'view' => $view,
                'recipient' => $email,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function destinationDepartmentManagerContact(string $stage): array
    {
        $stageDepartmentAliases = [
            'architect' => ['الهندسة', 'engineering', 'architect', 'design', 'التصاميم'],
            'purchasing' => ['المشتريات', 'purchasing', 'purchase'],
            'production_installation' => ['المصنع', 'factory', 'production'],
            'installation' => ['التركيبات', 'installation', 'installations'],
        ];

        $aliases = $stageDepartmentAliases[$stage] ?? [];
        if (empty($aliases)) {
            return [null, null];
        }

        $department = Department::query()
            ->with('managerUser')
            ->where(function ($query) use ($aliases) {
                foreach ($aliases as $alias) {
                    $query->orWhereRaw('LOWER(name) = ?', [mb_strtolower($alias)]);
                }
            })
            ->orderBy('id')
            ->first();

        if (! $department || ! $department->managerUser) {
            return [null, null];
        }

        $manager = $department->managerUser;
        if (! $manager->email || ! $manager->is_active || $manager->approval_status !== 'approved') {
            return [null, null];
        }

        return [$manager->email, $manager->id];
    }
}
