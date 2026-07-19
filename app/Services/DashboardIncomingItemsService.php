<?php

namespace App\Services;

use App\Models\ArchitectMaterialRequest;
use App\Models\Asset;
use App\Models\DismissedRequest;
use App\Models\Employee;
use App\Models\InstallationFactoryRequest;
use App\Models\Leave;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardIncomingItemsService
{
    public const VEHICLE_WINDOW_DAYS = 60;

    public const DOCUMENT_WINDOW_DAYS = 30;

    /** Passport reminders in «طلبات إليك»: only the linked employee; within 8 months before expiry (or already expired). */
    public const PASSPORT_WINDOW_MONTHS = 8;

    /**
     * @return array{items: Collection<int, array<string, mixed>>, count: int}
     */
    public function buildForUser(User $user): array
    {
        $today = Carbon::today();

        if ($user->isAdminLike()) {
            $items = $this->buildAdminAggregate($today);

            // Admin role should not receive other employees' residency alerts.
            // Keep only personal iqama reminder when the admin has a linked employee profile.
            if ($user->role === User::ROLE_ADMIN) {
                $items = $items
                    ->reject(fn (array $row) => str_starts_with((string) ($row['key'] ?? ''), 'employee_residency_'))
                    ->values()
                    ->merge($this->personalEmployeeIqamaAlerts($user, $today));
            }
        } else {
            $items = collect();

            if ($user->canAccessProcurementModule() || $user->canAccessContractPurchasesModule()) {
                $items = $items->merge($this->procurementMaterialRequests(15));
            }

            if ($user->canManageProduction()) {
                $items = $items->merge($this->factoryInstallationRequests(15));
            }

            if ($user->canAccessHRModule()) {
                $items = $items->merge($this->hrPendingLeaves(20));
                $items = $items->merge($this->hrResidencyAlerts($today, 12));
                $items = $items->merge($this->hrVehicleAlerts($today, 14));
            } elseif ($user->employee) {
                $items = $items->merge($this->personalEmployeeIqamaAlerts($user, $today));
            }

            if ($user->canAccessEngineeringModule()) {
                $items = $items->merge($this->engineeringArchitectQueue(8));
            }

            if ($user->role === 'operations_manager') {
                $items = $items->merge($this->operationsInstallationHighlights(6));
            }

            $items = $items->unique('key')->values();
        }

        if ($user->employee) {
            $items = $items->merge($this->personalPassportAlerts($user, $today));
        }

        $items = $items->unique('key')->values();

        $items = $this->excludeWhileHidden($items, $user);

        $items = $this->sortItems($items)->take(50)->values();

        return [
            'items' => $items,
            'count' => $items->count(),
        ];
    }

    /**
     * Hide uses explicit hidden_until: item stays out while hidden_until > now().
     *
     * @param  Collection<int, array<string, mixed>>  $items
     * @return Collection<int, array<string, mixed>>
     */
    private function excludeWhileHidden(Collection $items, User $user): Collection
    {
        $hiddenKeys = DismissedRequest::query()
            ->where('user_id', $user->id)
            ->where('hidden_until', '>', now())
            ->pluck('item_key');

        return $items
            ->reject(fn (array $row) => $hiddenKeys->contains($row['key']))
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function buildAdminAggregate(Carbon $today): Collection
    {
        $items = collect();
        $items = $items->merge($this->procurementMaterialRequests(8));
        $items = $items->merge($this->factoryInstallationRequests(8));
        $items = $items->merge($this->hrPendingLeaves(10));
        $items = $items->merge($this->hrResidencyAlerts($today, 6));
        $items = $items->merge($this->hrVehicleAlerts($today, 8));
        $items = $items->merge($this->engineeringArchitectQueue(5));
        $items = $items->merge($this->operationsInstallationHighlights(5));

        return $items->unique('key')->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function procurementMaterialRequests(int $limit): Collection
    {
        return ArchitectMaterialRequest::query()
            ->with(['project', 'creator'])
            ->where('status', 'submitted')
            ->latest('submitted_at')
            ->limit($limit)
            ->get()
            ->map(function (ArchitectMaterialRequest $r) {
                $projectName = $r->project?->name ?? '—';

                return $this->item(
                    'architect_material_request',
                    $r->id,
                    __('incoming.architect_material_pending'),
                    __('incoming.project_label').' '.$projectName,
                    __('incoming.request_num').$r->id.__('incoming.from_creator').($r->creator?->name ?? '—'),
                    __('incoming.sent_to_procurement'),
                    route('purchases.material-requests.show', $r),
                    $r->submitted_at ?? $r->created_at,
                    true
                );
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function factoryInstallationRequests(int $limit): Collection
    {
        return InstallationFactoryRequest::query()
            ->with(['project', 'creator'])
            ->where('status', InstallationFactoryRequest::STATUS_SUBMITTED)
            ->latest('submitted_at')
            ->limit($limit)
            ->get()
            ->map(function (InstallationFactoryRequest $r) {
                $projectName = $r->project?->name ?? '—';

                return $this->item(
                    'installation_factory_request',
                    $r->id,
                    __('incoming.installation_pending_factory'),
                    __('incoming.project_label').' '.$projectName,
                    __('incoming.request_num').$r->id.__('incoming.from_creator').($r->creator?->name ?? '—'),
                    __('factory.installation_status.'.$r->status),
                    route('factory.installation-requests.show', $r),
                    $r->submitted_at ?? $r->created_at,
                    true
                );
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function hrPendingLeaves(int $limit): Collection
    {
        return Leave::query()
            ->with('employee')
            ->where('status', 'pending')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (Leave $leave) {
                $name = $leave->employee?->name ?? '—';

                return $this->item(
                    'leave',
                    $leave->id,
                    __('incoming.leave_pending_approval'),
                    __('incoming.employee_label').' '.$name,
                    __('incoming.from_date').$leave->start_date?->format('Y-m-d').__('incoming.to_date').$leave->end_date?->format('Y-m-d'),
                    __('incoming.under_review'),
                    route('leaves.index'),
                    $leave->created_at ?? Carbon::now(),
                    true
                );
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function hrResidencyAlerts(Carbon $today, int $limit): Collection
    {
        $end = $today->copy()->addDays(self::DOCUMENT_WINDOW_DAYS);

        return Employee::query()
            ->whereNotNull('residency_expiry_date')
            ->where(function ($q) use ($today, $end) {
                $q->whereBetween('residency_expiry_date', [$today, $end])
                    ->orWhere('residency_expiry_date', '<', $today);
            })
            ->orderBy('residency_expiry_date')
            ->limit($limit)
            ->get()
            ->map(function (Employee $e) use ($today) {
                $exp = $e->residency_expiry_date ? Carbon::parse($e->residency_expiry_date)->startOfDay() : null;
                $expired = $exp && $exp->lt($today);
                $urgent = $expired || ($exp && $today->diffInDays($exp, false) <= 7);

                return $this->item(
                    'employee_residency',
                    $e->id,
                    $expired ? __('incoming.residency_expired_title') : __('incoming.residency_expiring_title'),
                    __('incoming.employee_label').' '.$e->name,
                    __('incoming.expiry_date').($e->residency_expiry_date ?? '—'),
                    $expired ? __('incoming.expired_status') : __('incoming.within_window'),
                    route('employees.show', $e),
                    $exp ?? Carbon::now(),
                    $urgent
                );
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function hrVehicleAlerts(Carbon $today, int $limit): Collection
    {
        $end = $today->copy()->addDays(self::VEHICLE_WINDOW_DAYS);

        return Asset::query()
            ->whereIn('asset_type', ['vehicle', 'مركبة'])
            ->where(function ($q) use ($today, $end) {
                $q->whereBetween('registration_expiry_date', [$today, $end])
                    ->orWhereBetween('inspection_expiry_date', [$today, $end]);
            })
            ->orderBy('registration_expiry_date')
            ->limit($limit)
            ->get()
            ->map(function (Asset $asset) use ($today) {
                $parts = [];
                $winEnd = $today->copy()->addDays(self::VEHICLE_WINDOW_DAYS);
                if ($asset->registration_expiry_date) {
                    $d = Carbon::parse($asset->registration_expiry_date)->startOfDay();
                    if ($d->gte($today) && $d->lte($winEnd)) {
                        $parts[] = __('incoming.registration_short').$d->format('Y-m-d');
                    }
                }
                if ($asset->inspection_expiry_date) {
                    $d = Carbon::parse($asset->inspection_expiry_date)->startOfDay();
                    if ($d->gte($today) && $d->lte($winEnd)) {
                        $parts[] = __('incoming.inspection_short').$d->format('Y-m-d');
                    }
                }
                $desc = implode(' — ', $parts) ?: __('incoming.vehicle_check_dates');

                return $this->item(
                    'vehicle',
                    $asset->id,
                    __('incoming.vehicle_alert_title'),
                    $asset->name . ' — ' . ($asset->plate_number ?? $asset->serial_number),
                    $desc,
                    __('incoming.within_days', ['days' => self::VEHICLE_WINDOW_DAYS]),
                    route('assets.show', $asset),
                    $asset->updated_at ?? $asset->created_at,
                    true
                );
            });
    }

    /**
     * إقامة الموظف لغير HR — نافذة 30 يومًا (منطق سابق مستقل عن الجواز).
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function personalEmployeeIqamaAlerts(User $user, Carbon $today): Collection
    {
        $emp = $user->employee;
        if (! $emp || ! $emp->residency_expiry_date) {
            return collect();
        }

        $end = $today->copy()->addDays(self::DOCUMENT_WINDOW_DAYS);
        $d = Carbon::parse($emp->residency_expiry_date)->startOfDay();
        if (! ($d->lte($end) || $d->lt($today))) {
            return collect();
        }

        return collect([
            $this->item(
                'personal_iqama',
                $emp->id,
                __('incoming.your_residency_reminder'),
                $emp->name,
                __('incoming.expires_on').$emp->residency_expiry_date,
                $d->lt($today) ? __('incoming.expired_status') : __('incoming.soon_status'),
                route('profile.show'),
                $d,
                $d->lt($today) || $today->diffInDays($d, false) <= 14
            ),
        ]);
    }

    /**
     * جواز السفر: يظهر للموظف صاحب الحساب المرتبط فقط، ضمن 8 أشهر قبل الانتهاء (أو منتهٍ).
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function personalPassportAlerts(User $user, Carbon $today): Collection
    {
        $emp = $user->employee;
        if (! $emp || ! $emp->passport_expiry_date) {
            return collect();
        }

        $d = Carbon::parse($emp->passport_expiry_date)->startOfDay();
        $winEnd = $today->copy()->addMonths(self::PASSPORT_WINDOW_MONTHS);

        $inWindow = $d->lt($today) || ($d->gte($today) && $d->lte($winEnd));
        if (! $inWindow) {
            return collect();
        }

        $expired = $d->lt($today);
        $urgent = $expired || ($today->diffInDays($d, false) <= 30);

        return collect([
            $this->item(
                'personal_passport',
                $emp->id,
                __('incoming.your_passport_reminder'),
                $emp->name,
                __('incoming.expires_on').$emp->passport_expiry_date.__('incoming.passport_window_note', ['months' => self::PASSPORT_WINDOW_MONTHS]),
                $expired ? __('incoming.passport_expired') : __('incoming.needs_followup'),
                route('profile.show'),
                $d,
                $urgent
            ),
        ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function engineeringArchitectQueue(int $limit): Collection
    {
        return Project::query()
            ->where('current_stage', 'architect')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (Project $project) {
                return $this->item(
                    'architect_queue',
                    $project->id,
                    __('incoming.project_architect_stage'),
                    __('incoming.project_label').' '.$project->name,
                    __('incoming.code_label').($project->project_code ?? '—').__('incoming.needs_design_followup'),
                    $project->status ?? '—',
                    route('architect-tasks.show', $project),
                    $project->updated_at ?? $project->created_at,
                    false
                );
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function operationsInstallationHighlights(int $limit): Collection
    {
        return Project::query()
            ->where('current_stage', 'production_installation')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (Project $project) {
                return $this->item(
                    'ops_install',
                    $project->id,
                    __('incoming.project_installation_stage'),
                    __('incoming.project_label').' '.$project->name,
                    __('incoming.follow_execution_delivery'),
                    $project->status ?? '—',
                    route('installations.show', $project),
                    $project->updated_at ?? $project->created_at,
                    false
                );
            });
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function sortItems(Collection $items): Collection
    {
        return $items->sort(function (array $a, array $b) {
            $ua = $a['urgent'] ?? false;
            $ub = $b['urgent'] ?? false;
            if ($ua !== $ub) {
                return $ua ? -1 : 1;
            }

            $ta = $a['sort_at'] ?? null;
            $tb = $b['sort_at'] ?? null;
            if ($ta instanceof Carbon && $tb instanceof Carbon) {
                return $tb <=> $ta;
            }

            return 0;
        })->values();
    }

    private function item(
        string $type,
        string|int $id,
        string $title,
        string $meta,
        string $description,
        ?string $status,
        string $url,
        mixed $sortAt,
        bool $urgent
    ): array {
        $key = $type.'_'.$id;

        $sortAt = $sortAt instanceof Carbon ? $sortAt : Carbon::parse($sortAt);

        return [
            'key' => $key,
            'title' => $title,
            'meta' => $meta,
            'description' => $description,
            'status' => $status,
            'url' => $url,
            'sort_at' => $sortAt,
            'created_label' => $sortAt->format('Y-m-d H:i'),
            'created_human' => $sortAt->diffForHumans(),
            'urgent' => $urgent,
        ];
    }
}
