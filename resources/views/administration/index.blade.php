@extends('layouts.app')

@section('page_title', __('administration.page_title'))
@section('page_subtitle', __('administration.page_subtitle'))

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 class="page-title">{{ __('administration.page_title') }}</h1>
            <p class="page-subtitle">{{ __('administration.index_intro') }}</p>
        </div>
        <div class="actions-row">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">{{ __('administration.dashboard_link') }}</a>
        </div>
    </div>

    <div class="stats-grid" style="margin-top:12px;">
        <div class="stat-card">
            <div class="stat-label">{{ __('administration.stat_total_projects') }}</div>
            <div class="stat-value">{{ $totalProjects }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">{{ __('administration.stat_portal_users') }}</div>
            <div class="stat-value">{{ $totalClientUsers }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">{{ __('administration.stat_employees') }}</div>
            <div class="stat-value">{{ $totalEmployees }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">{{ __('administration.stat_residency_expiring') }}</div>
            <div class="stat-value">{{ $upcomingResidencyCount }}</div>
        </div>
    </div>

    <div class="page-header" style="margin-top:16px;">
        <h2 class="page-title" style="font-size:15px;">{{ __('administration.project_status_summary') }}</h2>
    </div>
    <div class="details-grid">
        @forelse($statusSummary as $status => $count)
            <div class="detail-box">
                <div class="stat-label">{{ $status }}</div>
                <div class="stat-value" style="font-size:22px;">{{ $count }}</div>
            </div>
        @empty
            <div class="detail-box"><span class="empty-row">{{ __('administration.no_projects') }}</span></div>
        @endforelse
    </div>

    <div class="page-header" style="margin-top:20px;">
        <h2 class="page-title" style="font-size:15px;">{{ __('administration.shortcuts_title') }}</h2>
    </div>
    <div class="actions-row" style="margin-top:8px; flex-wrap:wrap;">
        <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">{{ __('administration.employees_link') }}</a>
        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">{{ __('administration.users_link') }}</a>
        <a href="{{ route('administration.assignments') }}" class="btn btn-secondary btn-sm">{{ __('administration.assignments_link') }}</a>
        <a href="{{ route('engineering-projects.index') }}" class="btn btn-secondary btn-sm">{{ __('administration.engineering_projects_link') }}</a>
        <a href="{{ route('administration.updates') }}" class="btn btn-secondary btn-sm">{{ __('administration.updates_link') }}</a>
    </div>

    <div class="page-header" style="margin-top:20px;">
        <h2 class="page-title" style="font-size:15px;">{{ __('administration.recent_updates_title') }}</h2>
    </div>
    <div class="table-wrap" style="margin-top:8px;">
        <table>
            <thead>
                <tr>
                    <th>{{ __('administration.th_date') }}</th>
                    <th>{{ __('administration.th_project') }}</th>
                    <th>{{ __('administration.th_title') }}</th>
                    <th>{{ __('administration.th_progress') }}</th>
                    <th>{{ __('administration.th_recorded_by') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentUpdates as $u)
                    <tr>
                        <td>{{ $u->created_at?->format('Y-m-d H:i') }}</td>
                        <td>{{ $u->project->name ?? '—' }}</td>
                        <td>{{ $u->title }}</td>
                        <td>{{ (int) $u->progress }}٪</td>
                        <td>{{ $u->creator?->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-row">{{ __('administration.no_updates') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="page-header" style="margin-top:20px;">
        <h2 class="page-title" style="font-size:15px;">{{ __('administration.internal_notifications_title') }}</h2>
    </div>
    <div class="table-wrap" style="margin-top:8px;">
        <table>
            <thead>
                <tr>
                    <th>{{ __('administration.th_title') }}</th>
                    <th>{{ __('administration.th_message') }}</th>
                    <th>{{ __('administration.th_date') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($adminInternalNotifications as $notification)
                    <tr>
                        <td>{{ $notification->title }}</td>
                        <td>{{ $notification->message ?? '—' }}</td>
                        <td>{{ $notification->created_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="empty-row">{{ __('administration.no_internal_notifications') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
