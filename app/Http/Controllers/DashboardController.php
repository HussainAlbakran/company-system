<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Project;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $monthlySalaryBudget = 0;
        $currentProjectsValue = 0;
        $currentProjectsExpenses = 0;
        $remainingProjectsBudget = 0;
        $delayedProjectsCount = 0;
        $endingSoonProjectsCount = 0;
        $adminProjects = collect();

        $residencyExpiringEmployees = collect();
        $expiredResidencyEmployees = collect();
        $employeeResidencyAlert = null;

        $user = auth()->user();

        if ($user && $user->isAdmin()) {
            $monthlySalaryBudget = (float) Employee::sum('salary');

            $currentProjectsQuery = Project::query()
                ->where(function ($query) {
                    $query->whereNull('status')
                        ->orWhere('status', 'ongoing')
                        ->orWhere('status', 'active')
                        ->orWhere('status', 'in progress')
                        ->orWhere('status', 'قائم')
                        ->orWhere('status', 'مستمر')
                        ->orWhere('status', 'قيد التنفيذ');
                });

            $currentProjectsValue = (float) (clone $currentProjectsQuery)->sum('project_value');
            $currentProjectsExpenses = (float) (clone $currentProjectsQuery)->sum('expenses');
            $remainingProjectsBudget = $currentProjectsValue - $currentProjectsExpenses;

            $adminProjects = (clone $currentProjectsQuery)
                ->with(['department', 'responsibleEmployee'])
                ->latest()
                ->get()
                ->map(function ($project) {
                    $today = Carbon::today();
                    $daysRemaining = null;
                    $isDelayed = false;
                    $isEndingSoon = false;

                    if (!empty($project->end_date)) {
                        try {
                            $endDate = Carbon::parse($project->end_date);
                            $daysRemaining = $today->diffInDays($endDate, false);

                            if ($daysRemaining < 0) {
                                $isDelayed = true;
                            }

                            if ($daysRemaining >= 0 && $daysRemaining <= 7) {
                                $isEndingSoon = true;
                            }
                        } catch (\Exception $e) {
                            $daysRemaining = null;
                        }
                    }

                    $project->days_remaining = $daysRemaining;
                    $project->is_delayed = $isDelayed;
                    $project->is_ending_soon = $isEndingSoon;
                    $project->remaining_budget = (float) ($project->project_value ?? 0) - (float) ($project->expenses ?? 0);

                    return $project;
                });

            $delayedProjectsCount = $adminProjects->where('is_delayed', true)->count();
            $endingSoonProjectsCount = $adminProjects->where('is_ending_soon', true)->count();
        }

        if ($user && ($user->isAdmin() || $user->isHR())) {
            $today = Carbon::today();
            $after30Days = Carbon::today()->addDays(30);

            $residencyExpiringEmployees = Employee::with('department')
                ->whereNotNull('residency_expiry_date')
                ->whereDate('residency_expiry_date', '>=', $today)
                ->whereDate('residency_expiry_date', '<=', $after30Days)
                ->orderBy('residency_expiry_date', 'asc')
                ->get()
                ->map(function ($employee) use ($today) {
                    $employee->residency_days_remaining = $today->diffInDays(
                        Carbon::parse($employee->residency_expiry_date),
                        false
                    );

                    return $employee;
                });

            $expiredResidencyEmployees = Employee::with('department')
                ->whereNotNull('residency_expiry_date')
                ->whereDate('residency_expiry_date', '<', $today)
                ->orderBy('residency_expiry_date', 'asc')
                ->get()
                ->map(function ($employee) use ($today) {
                    $employee->residency_days_remaining = $today->diffInDays(
                        Carbon::parse($employee->residency_expiry_date),
                        false
                    );

                    return $employee;
                });
        }

        if ($user && ! $user->isAdmin() && ! $user->isHR()) {
            $employee = Employee::where('user_id', $user->id)->first();

            if ($employee && !empty($employee->residency_expiry_date)) {
                $today = Carbon::today();
                $expiryDate = Carbon::parse($employee->residency_expiry_date);
                $daysRemaining = $today->diffInDays($expiryDate, false);

                if ($daysRemaining <= 30) {
                    $employee->residency_days_remaining = $daysRemaining;
                    $employeeResidencyAlert = $employee;
                }
            }
        }

        return view('dashboard', compact(
            'monthlySalaryBudget',
            'currentProjectsValue',
            'currentProjectsExpenses',
            'remainingProjectsBudget',
            'delayedProjectsCount',
            'endingSoonProjectsCount',
            'adminProjects',
            'residencyExpiringEmployees',
            'expiredResidencyEmployees',
            'employeeResidencyAlert'
        ));
    }
}