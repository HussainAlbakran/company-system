<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Project;
use App\Models\ProductionOrder;
use App\Models\Purchase;
use App\Models\Installation;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // =========================
        // 🔥 متغيرات عامة
        // =========================
        $monthlySalaryBudget = 0;
        $currentProjectsValue = 0;
        $currentProjectsExpenses = 0;
        $delayedProjectsCount = 0;
        $endingSoonProjectsCount = 0;
        $adminProjects = collect();

        // =========================
        // 🔥 Counts للأقسام
        // =========================
        $architectProjectsCount = 0;
        $productionOrdersCount = 0;
        $installationProjectsCount = 0;
        $purchasesCount = 0;
        $employeesCount = 0;

        // =========================
        // 🔔 الإقامات
        // =========================
        $residencyExpiringEmployees = collect();
        $expiredResidencyEmployees = collect();

        // =====================================================
        // 🔥 ADMIN (يشوف كل شي)
        // =====================================================
        if ($user && $user->isAdmin()) {

            $monthlySalaryBudget = (float) Employee::sum('salary');

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
        }

        // =====================================================
        // 🔥 التصاميم
        // =====================================================
        if ($user && ($user->role == 'admin' || $user->role == 'engineer')) {
            $architectProjectsCount = Project::where('current_stage', 'architect')->count();
        }

        // =====================================================
        // 🔥 المصنع
        // =====================================================
        if ($user && $user->canManageProduction()) {
            $productionOrdersCount = ProductionOrder::count();
        }

        // =====================================================
        // 🔥 التركيبات
        // =====================================================
        if ($user && ($user->role == 'admin' || $user->role == 'manager')) {
            $installationProjectsCount = Project::where('current_stage', 'production_installation')->count();
        }

        // =====================================================
        // 🔥 المشتريات
        // =====================================================
        if ($user && ($user->role == 'admin' || $user->role == 'manager')) {
            $purchasesCount = Purchase::count();
        }

        // =====================================================
        // 🔥 الموظفين
        // =====================================================
        if ($user && $user->canManageEmployees()) {
            $employeesCount = Employee::count();
        }

        // =====================================================
        // 🔔 الإقامات (HR فقط)
        // =====================================================
        if ($user && $user->role == 'hr') {

            $today = Carbon::today();
            $after30 = Carbon::today()->addDays(30);

            $residencyExpiringEmployees = Employee::whereBetween('residency_expiry_date', [$today, $after30])->get();
            $expiredResidencyEmployees = Employee::where('residency_expiry_date', '<', $today)->get();
        }

        return view('dashboard', compact(
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
            'expiredResidencyEmployees'
        ));
    }
}