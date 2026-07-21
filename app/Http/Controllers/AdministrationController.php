<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\InternalNotification;
use App\Models\Project;
use App\Models\ProjectUpdate;
use App\Models\User;
use Illuminate\Contracts\View\View;

class AdministrationController extends Controller
{
    private function assertAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403, __('common.unauthorized'));
    }

    public function index(): View
    {
        $this->assertAdmin();

        $totalProjects = Project::count();
        $totalClientUsers = User::query()
            ->whereIn('id', Project::query()->whereNotNull('client_user_id')->select('client_user_id'))
            ->count();
        $totalEmployees = Employee::count();
        $statusSummary = Project::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $recentUpdates = ProjectUpdate::query()
            ->with(['project:id,name', 'creator:id,name'])
            ->latest()
            ->limit(10)
            ->get();

        $today = now()->toDateString();
        $after30 = now()->addDays(30)->toDateString();
        $upcomingResidencyCount = Employee::query()
            ->whereBetween('residency_expiry_date', [$today, $after30])
            ->count();
        $upcomingPassportCount = Employee::query()
            ->whereBetween('passport_expiry_date', [$today, $after30])
            ->count();

        $adminInternalNotifications = InternalNotification::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->limit(10)
            ->get();

        return view('administration.index', compact(
            'totalProjects',
            'totalClientUsers',
            'totalEmployees',
            'statusSummary',
            'recentUpdates',
            'upcomingResidencyCount',
            'upcomingPassportCount',
            'adminInternalNotifications'
        ));
    }

    public function assignments(): View
    {
        $this->assertAdmin();

        $projects = Project::query()
            ->with([
                'responsibleEmployee:id,name',
                'clientUser:id,name,email',
            ])
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('administration.assignments', compact('projects'));
    }

    public function updates(): View
    {
        $this->assertAdmin();

        $updates = ProjectUpdate::query()
            ->with(['project:id,name', 'creator:id,name'])
            ->latest()
            ->paginate(25);

        return view('administration.updates', compact('updates'));
    }
}
