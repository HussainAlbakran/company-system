<?php

namespace App\Http\Controllers;

use App\Models\ArchitectMaterialRequest;
use App\Models\Employee;
use App\Models\InstallationFactoryRequest;
use App\Models\InternalNotification;
use App\Models\Leave;
use App\Models\ProductionOrder;
use App\Models\Project;
use App\Models\ProjectUpdate;
use App\Models\Purchase;
use App\Models\DismissedRequest;
use App\Models\User;
use App\Services\DashboardIncomingItemsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dismiss(Request $request)
    {
        $validated = $request->validate([
            'item_key' => ['required', 'string', 'max:255'],
        ]);

        $hiddenUntil = now()->copy()->addDay()->startOfDay()->addHours(7);

        DismissedRequest::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'item_key' => $validated['item_key'],
            ],
            [
                'dismissed_at' => now(),
                'hidden_until' => $hiddenUntil,
            ]
        );

        return back();
    }

    public function index(DashboardIncomingItemsService $incomingItemsService)
    {
        $user = auth()->user();

        if ($user && $user->isBasicUser()) {
            return view('dashboard-user');
        }

        if ($user && $user->isFinance()) {
            $incomingPayload = $incomingItemsService->buildForUser($user);

            return view('dashboard', [
                'dashboardIsAdmin' => false,
                'financeDashboard' => true,
                'monthlySalaryBudget' => 0,
                'currentProjectsValue' => 0,
                'currentProjectsExpenses' => 0,
                'delayedProjectsCount' => 0,
                'endingSoonProjectsCount' => 0,
                'adminProjects' => collect(),
                'architectProjectsCount' => 0,
                'productionOrdersCount' => 0,
                'installationProjectsCount' => 0,
                'purchasesCount' => 0,
                'employeesCount' => 0,
                'residencyExpiringEmployees' => collect(),
                'expiredResidencyEmployees' => collect(),
                'passportExpiringEmployees' => collect(),
                'expiredPassportEmployees' => collect(),
                'internalNotifications' => InternalNotification::query()
                    ->where('user_id', $user->id)
                    ->latest()
                    ->limit(12)
                    ->get(),
                'adminOverviewTotalProjects' => 0,
                'adminOverviewTotalClients' => 0,
                'adminOverviewTotalEmployees' => 0,
                'adminOverviewStatusSummary' => collect(),
                'adminOverviewRecentUpdates' => collect(),
                'procurementKpi' => null,
                'engineeringKpi' => null,
                'operationsKpi' => null,
                'hrKpi' => null,
                'incomingRequestsItems' => $incomingPayload['items'],
                'incomingRequestsCount' => $incomingPayload['count'],
            ]);
        }

        $dashboardIsAdmin = $user && $user->isAdminLike();

        $monthlySalaryBudget = 0;
        $currentProjectsValue = 0;
        $currentProjectsExpenses = 0;
        $delayedProjectsCount = 0;
        $endingSoonProjectsCount = 0;
        $adminProjects = collect();

        $architectProjectsCount = 0;
        $productionOrdersCount = 0;
        $installationProjectsCount = 0;
        $purchasesCount = 0;
        $employeesCount = 0;

        $residencyExpiringEmployees = collect();
        $expiredResidencyEmployees = collect();
        $passportExpiringEmployees = collect();
        $expiredPassportEmployees = collect();
        $internalNotifications = collect();

        $adminOverviewTotalProjects = 0;
        $adminOverviewTotalClients = 0;
        $adminOverviewTotalEmployees = 0;
        $adminOverviewStatusSummary = collect();
        $adminOverviewRecentUpdates = collect();

        $procurementKpi = null;
        $engineeringKpi = null;
        $operationsKpi = null;
        $hrKpi = null;

        if ($dashboardIsAdmin) {

            $monthlySalaryBudget = (float) Employee::sum('salary');

            $adminOverviewTotalProjects = Project::count();
            $adminOverviewTotalClients = User::query()
                ->whereIn('id', Project::query()->whereNotNull('client_user_id')->select('client_user_id'))
                ->count();
            $adminOverviewTotalEmployees = Employee::count();
            $adminOverviewStatusSummary = Project::query()
                ->selectRaw('status, COUNT(*) as aggregate')
                ->groupBy('status')
                ->pluck('aggregate', 'status');
            $adminOverviewRecentUpdates = ProjectUpdate::query()
                ->with(['project:id,name', 'creator:id,name'])
                ->latest()
                ->limit(8)
                ->get();

            $currentProjects = Project::where('status', 'ongoing')->get();

            $currentProjectsValue = (float) $currentProjects->sum('project_value');
            $currentProjectsExpenses = (float) $currentProjects->sum('expenses');

            $adminProjects = Project::with(['department', 'responsibleEmployee'])
                ->latest()
                ->get()
                ->map(function ($project) {

                    $today = Carbon::today();

                    if ($project->end_date) {
                        $days = Carbon::parse($project->end_date)->diffInDays($today, false);
                        $project->days_remaining = $days;
                        $project->is_delayed = $days < 0;
                        $project->is_ending_soon = $days >= 0 && $days <= 7;
                    }

                    return $project;
                });

            $delayedProjectsCount = $adminProjects->where('is_delayed', true)->count();
            $endingSoonProjectsCount = $adminProjects->where('is_ending_soon', true)->count();

            $architectProjectsCount = Project::where('current_stage', 'architect')->count();
            $productionOrdersCount = ProductionOrder::count();
            $installationProjectsCount = Project::where('current_stage', 'production_installation')->count();
            $purchasesCount = Purchase::count();
            $employeesCount = Employee::count();
        } else {
            if ($user->canAccessEngineeringModule()) {
                $architectProjectsCount = Project::where('current_stage', 'architect')->count();
                $engineeringKpi = [
                    'architect_projects' => $architectProjectsCount,
                ];
            }

            if ($user->canManageProduction()) {
                $productionOrdersCount = ProductionOrder::count();
            }

            if ($user->canManageInstallations()) {
                $installationProjectsCount = Project::where('current_stage', 'production_installation')->count();
            }

            if ($user->canAccessProcurementModule()) {
                $purchasesCount = Purchase::count();
                $procurementKpi = [
                    'pending_material_requests' => ArchitectMaterialRequest::query()
                        ->where('status', 'submitted')
                        ->count(),
                    'contract_purchases' => Purchase::query()
                        ->where('type', 'contract_purchase')
                        ->count(),
                    'general_purchases' => Purchase::query()
                        ->whereIn('type', ['asset_purchase', 'general_maintenance'])
                        ->count(),
                    'purchase_records_total' => Purchase::query()->count(),
                ];
            }

            if ($user->canAccessHRModule()) {
                $employeesCount = Employee::count();
                $today = Carbon::today();
                $after70 = Carbon::today()->addDays(70);
                $residencyExpiringEmployees = Employee::whereBetween('residency_expiry_date', [$today, $after70])->get();
                $expiredResidencyEmployees = Employee::where('residency_expiry_date', '<', $today)->get();
                $hrKpi = [
                    'employees' => $employeesCount,
                    'pending_leaves' => Leave::query()->where('status', 'pending')->count(),
                    'residency_expiring_soon' => $residencyExpiringEmployees->count(),
                    'residency_expired' => $expiredResidencyEmployees->count(),
                ];
            }

            if ($user->canManageProduction() || $user->canManageInstallations()) {
                $operationsKpi = [
                    'production_orders' => $user->canManageProduction()
                        ? ProductionOrder::query()->count()
                        : 0,
                    'installation_projects' => $user->canManageInstallations()
                        ? Project::query()->where('current_stage', 'production_installation')->count()
                        : 0,
                    'installation_factory_pending' => $user->canManageProduction()
                        ? InstallationFactoryRequest::query()
                            ->where('status', InstallationFactoryRequest::STATUS_SUBMITTED)
                            ->count()
                        : 0,
                ];
            }
        }

        if ($dashboardIsAdmin && $user && $user->canAccessHRModule()) {

            $today = Carbon::today();
            $after70 = Carbon::today()->addDays(70);

            $residencyExpiringEmployees = Employee::whereBetween('residency_expiry_date', [$today, $after70])->get();
            $expiredResidencyEmployees = Employee::where('residency_expiry_date', '<', $today)->get();
        }

        if ($user) {
            $internalNotifications = InternalNotification::query()
                ->where('user_id', $user->id)
                ->latest()
                ->limit(12)
                ->get();
        }

        $incomingPayload = $user
            ? $incomingItemsService->buildForUser($user)
            : ['items' => collect(), 'count' => 0];
        $incomingRequestsItems = $incomingPayload['items'];
        $incomingRequestsCount = $incomingPayload['count'];

        return view('dashboard', compact(
            'dashboardIsAdmin',
            'procurementKpi',
            'engineeringKpi',
            'operationsKpi',
            'hrKpi',

            'monthlySalaryBudget',
            'currentProjectsValue',
            'currentProjectsExpenses',
            'delayedProjectsCount',
            'endingSoonProjectsCount',
            'adminProjects',

            'architectProjectsCount',
            'productionOrdersCount',
            'installationProjectsCount',
            'purchasesCount',
            'employeesCount',

            'residencyExpiringEmployees',
            'expiredResidencyEmployees',
            'passportExpiringEmployees',
            'expiredPassportEmployees',
            'internalNotifications',

            'adminOverviewTotalProjects',
            'adminOverviewTotalClients',
            'adminOverviewTotalEmployees',
            'adminOverviewStatusSummary',
            'adminOverviewRecentUpdates',

            'incomingRequestsItems',
            'incomingRequestsCount'
        ));
    }
}
