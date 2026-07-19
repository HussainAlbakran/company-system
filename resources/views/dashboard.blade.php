@extends('layouts.app')

@section('page_title', __('dashboard.page_title'))
@section('page_subtitle', !empty($dashboardIsAdmin) ? __('dashboard.subtitle_admin') : __('dashboard.subtitle_role'))

@section('content')
@php
    $u = auth()->user();
@endphp

<style>
    .dashboard-stack .dash-section-title {
        font-size: 11px;
        font-weight: 700;
        color: #111827;
    }

    .dashboard-stack .dash-section-sub {
        margin-top: 6px;
        font-size: 11px;
        color: #111827;
    }

    .dashboard-stack .dash-section-note {
        margin-top: 4px;
        font-size: 10px;
        color: #374151;
    }
</style>

<div class="dashboard-stack">
    <section class="dashboard-panel">
        <div class="panel-head">
            <div>
                <h3 class="panel-title">{{ __('dashboard.executive_command_center') }}</h3>
                <p class="panel-subtitle">{{ __('dashboard.summary_for_user', ['name' => $u->name]) }}</p>
            </div>
            <span class="badge badge-blue">{{ __('dashboard.live') }}</span>
        </div>
    </section>

    <section class="dashboard-panel" style="border-color: rgba(245, 158, 11, 0.40);">
        <div class="panel-head">
            <div>
                <h3 class="panel-title">{{ __('dashboard.incoming_for_you') }}</h3>
                <p class="panel-subtitle">
                    @if(($incomingRequestsCount ?? 0) > 0)
                        @if(($incomingRequestsCount ?? 0) === 1)
                            {!! __('dashboard.incoming_count_one', ['count' => '<strong>'.$incomingRequestsCount.'</strong>']) !!}
                        @else
                            {!! __('dashboard.incoming_count_many', ['count' => '<strong>'.$incomingRequestsCount.'</strong>']) !!}
                        @endif
                    @else
                        {{ __('dashboard.no_incoming_items') }}
                    @endif
                </p>
            </div>
            <span class="badge badge-orange">{{ __('dashboard.incoming_badge') }}</span>
        </div>

        @if(isset($incomingRequestsItems) && $incomingRequestsItems->isNotEmpty())
            <div class="panel-grid-2">
                @foreach($incomingRequestsItems as $row)
                    <article class="detail-box" @if(!empty($row['urgent'])) style="border-color: rgba(239, 68, 68, 0.50);" @endif>
                        <div style="font-weight:700;">{{ $row['title'] }}</div>
                        <div class="panel-subtitle">{{ $row['meta'] }}</div>
                        <div style="margin-top:6px;">{{ $row['description'] }}</div>
                        <div class="actions-row" style="margin-top:8px; justify-content:space-between;">
                            <div>
                                @if(!empty($row['status']))
                                    <span class="badge badge-blue">{{ $row['status'] }}</span>
                                @endif
                            </div>
                            <div class="actions-row">
                                <form method="POST" action="{{ route('dashboard.dismiss') }}" style="margin:0;">
                                    @csrf
                                    <input type="hidden" name="item_key" value="{{ $row['key'] }}">
                                    <button type="submit" class="btn btn-secondary btn-sm" title="{{ __('dashboard.hide_until_7am') }}" aria-label="{{ __('dashboard.hide_until_7am') }}">{{ __('dashboard.hide') }}</button>
                                </form>
                                <a href="{{ $row['url'] }}" class="btn btn-primary btn-sm">{{ __('dashboard.view_open') }}</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="detail-box empty-row">{{ __('dashboard.no_incoming_items') }}</div>
        @endif
    </section>

    <section class="dashboard-panel">
        <div class="panel-head">
            <div>
                <h3 class="panel-title">{{ __('dashboard.internal_notifications') }}</h3>
                <p class="panel-subtitle">{{ __('dashboard.internal_notifications_sub') }}</p>
            </div>
            <span class="badge badge-blue">{{ __('dashboard.alert_badge') }}</span>
        </div>

        @if(isset($internalNotifications) && $internalNotifications->isNotEmpty())
            <div class="panel-grid-2">
                @foreach($internalNotifications as $notification)
                    <article class="detail-box">
                        <div style="font-weight:700;">{{ $notification->title }}</div>
                        @if(filled($notification->message ?? null))
                            <div style="margin-top:6px;">{{ $notification->message }}</div>
                        @endif
                        <div class="panel-subtitle" style="margin-top:6px;">{{ $notification->created_at?->diffForHumans() }}</div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <div class="stats-grid" style="margin-bottom:8px;">
        <div class="stat-card" style="border-color: rgba(59,130,246,.48);">
            <div class="stat-label">{{ __('dashboard.total_projects') }}</div>
            <div class="stat-value">{{ auth()->user()->isAdmin() ? ($adminProjects->count() ?? 0) : ($architectProjectsCount ?? 0) + ($installationProjectsCount ?? 0) }}</div>
            <div class="stat-note">{{ __('dashboard.portfolio_size') }}</div>
        </div>

        <div class="stat-card" style="border-color: rgba(34,197,94,.48);">
            <div class="stat-label">{{ __('dashboard.active_projects') }}</div>
            <div class="stat-value">{{ $architectProjectsCount + $installationProjectsCount }}</div>
            <div class="stat-note">{{ __('dashboard.in_progress') }}</div>
        </div>

        <div class="stat-card" style="border-color: rgba(168,85,247,.48);">
            <div class="stat-label">{{ __('dashboard.production_orders') }}</div>
            <div class="stat-value">{{ $productionOrdersCount ?? 0 }}</div>
            <div class="stat-note">{{ __('dashboard.factory_track') }}</div>
        </div>

        <div class="stat-card" style="border-color: rgba(239,68,68,.48);">
            <div class="stat-label">{{ __('dashboard.operational_risks') }}</div>
            <div class="stat-value">{{ $delayedProjectsCount + $endingSoonProjectsCount }}</div>
            <div class="stat-note">{{ __('dashboard.schedule_pressure') }}</div>
        </div>
    </div>

    <div class="stats-grid" style="margin-bottom:8px;">
        @if($delayedProjectsCount > 0)
            <div class="stat-card">
                <div class="stat-label">{{ __('dashboard.delayed_projects') }}</div>
                <div class="stat-value" style="color:#f9a3a3;">{{ $delayedProjectsCount }}</div>
            </div>
        @endif

        @if($endingSoonProjectsCount > 0)
            <div class="stat-card">
                <div class="stat-label">{{ __('dashboard.ending_soon') }}</div>
                <div class="stat-value" style="color:#f6cf7a;">{{ $endingSoonProjectsCount }}</div>
            </div>
        @endif

        @if(auth()->user()->role == 'hr')
            <div class="stat-card">
                <div class="stat-label">{{ __('dashboard.residency_alerts') }}</div>
                <div class="stat-value" style="color:#ccb0ff;">
                    {{ $expiredResidencyEmployees->count() + $residencyExpiringEmployees->count() }}
                </div>
            </div>
        @endif
    </div>

    <div class="details-grid" style="margin-bottom:8px;">
        <div class="detail-box">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:8px;">
                <strong class="dash-section-title">{{ __('dashboard.execution') }}</strong>
                <span class="badge badge-blue">{{ __('dashboard.live') }}</span>
            </div>
            <div class="dash-section-sub">
                {{ __('dashboard.factory_installations_path') }}
            </div>
            <div class="actions-row" style="margin-top:7px;">
                <a href="{{ route('factory.index') }}" class="btn btn-primary btn-sm">{{ __('navigation.factory') }}</a>
                <a href="{{ route('installations.index') }}" class="btn btn-success btn-sm">{{ __('navigation.installations') }}</a>
            </div>
        </div>

        <div class="detail-box">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:8px;">
                <strong class="dash-section-title">{{ __('dashboard.commercial_procurement') }}</strong>
                <span class="badge badge-gray">{{ __('dashboard.commercial_operations') }}</span>
            </div>
            <div class="dash-section-sub">
                {{ __('dashboard.contracts_and_purchases') }}
            </div>
            <div class="actions-row" style="margin-top:7px;">
                <a href="{{ route('sales-contracts.index') }}" class="btn btn-secondary btn-sm">{{ __('navigation.contracts') }}</a>
                @if(auth()->user()->role == 'admin' || auth()->user()->role == 'manager')
                    <a href="{{ route('purchases.index') }}" class="btn btn-warning btn-sm">{{ __('navigation.contract_purchases') }}</a>
                @endif
            </div>
        </div>

        <div class="detail-box">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:8px;">
                <strong class="dash-section-title">{{ __('dashboard.compliance_resources') }}</strong>
                <span class="badge badge-red">{{ __('dashboard.needs_action') }}</span>
            </div>
            <div class="dash-section-sub">
                {{ __('dashboard.hr_and_governance') }}
            </div>
            <div class="actions-row" style="margin-top:7px;">
                <a href="{{ route('leaves.index') }}" class="btn btn-secondary btn-sm">{{ __('navigation.leave_management') }}</a>
                @if(auth()->user()->canViewAuditLogs())
                    <a href="{{ route('audit.index') }}" class="btn btn-danger btn-sm">{{ __('navigation.audit_log') }}</a>
                @endif
            </div>
        </div>
    </div>

    <div class="details-grid" style="margin-bottom:8px;">
        <div class="detail-box">
            <strong class="dash-section-title">{{ __('dashboard.workload_distribution') }}</strong>
            <div style="margin-top:6px;">
                <x-ui.progress :value="min(($architectProjectsCount ?? 0) * 10, 100)" color="#3b82f6" />
            </div>
            <div class="dash-section-note">{{ __('dashboard.design_planning_load') }}</div>
        </div>

        <div class="detail-box">
            <strong class="dash-section-title">{{ __('dashboard.delivery_readiness') }}</strong>
            <div style="margin-top:6px;">
                <x-ui.progress :value="min(($installationProjectsCount ?? 0) * 10, 100)" color="#22c55e" />
            </div>
            <div class="dash-section-note">{{ __('dashboard.installation_readiness') }}</div>
        </div>

        <div class="detail-box">
            <strong class="dash-section-title">{{ __('dashboard.risk_pressure') }}</strong>
            <div style="margin-top:6px;">
                <x-ui.progress :value="min((($delayedProjectsCount + $endingSoonProjectsCount) ?? 0) * 12, 100)" color="#ef4444" />
            </div>
            <div class="dash-section-note">{{ __('dashboard.schedule_pressure_trend') }}</div>
        </div>
    </div>

    @if($u->isAdmin())
        <x-ui.card title="{{ __('dashboard.project_followup_list') }}" subtitle="{{ __('dashboard.priority_projects') }}">
            <x-ui.table>
                <thead>
                    <tr>
                        <th>{{ __('dashboard.project') }}</th>
                        <th>{{ __('dashboard.department') }}</th>
                        <th>{{ __('dashboard.responsible') }}</th>
                        <th>{{ __('common.end_date') }}</th>
                        <th>{{ __('dashboard.status') }}</th>
                        <th>{{ __('common.notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adminProjects->take(8) as $project)
                        <tr>
                            <td>{{ $project->name }}</td>
                            <td>{{ $project->department->name ?? '-' }}</td>
                            <td>{{ $project->responsibleEmployee->name ?? '-' }}</td>
                            <td>{{ $project->end_date ?? '-' }}</td>
                            <td>
                                @if($project->is_delayed)
                                    <span class="badge badge-red">{{ __('dashboard.delayed') }}</span>
                                @elseif($project->is_ending_soon)
                                    <span class="badge badge-orange">{{ __('dashboard.ending_soon_badge') }}</span>
                                @else
                                    <span class="badge badge-green">{{ __('dashboard.on_track') }}</span>
                                @endif
                            </td>
                            <td>{{ $project->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-row">{{ __('dashboard.no_recent_projects') }}</td></tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </x-ui.card>
    @endif
</div>
@endsection
