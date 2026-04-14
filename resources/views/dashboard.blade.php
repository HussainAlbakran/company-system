@extends('layouts.app')

@section('page_title', 'Dashboard')
@section('page_subtitle', 'Construction command center overview')

@section('content')
<x-ui.card title="ERP Control Center" subtitle="مرحبًا {{ auth()->user()->name }} - نظرة تنفيذية لحظية">

    <div class="stats-grid" style="margin-bottom:8px;">
        <div class="stat-card" style="border-color: rgba(59,130,246,.48); background: linear-gradient(135deg, rgba(59,130,246,.22), rgba(59,130,246,.05));">
            <div class="stat-label">Total Projects</div>
            <div class="stat-value">{{ auth()->user()->isAdmin() ? ($adminProjects->count() ?? 0) : ($architectProjectsCount ?? 0) + ($installationProjectsCount ?? 0) }}</div>
            <div class="stat-note">portfolio size</div>
        </div>

        <div class="stat-card" style="border-color: rgba(34,197,94,.48); background: linear-gradient(135deg, rgba(34,197,94,.22), rgba(34,197,94,.05));">
            <div class="stat-label">Active Projects</div>
            <div class="stat-value">{{ $architectProjectsCount + $installationProjectsCount }}</div>
            <div class="stat-note">live execution</div>
        </div>

        <div class="stat-card" style="border-color: rgba(168,85,247,.48); background: linear-gradient(135deg, rgba(168,85,247,.22), rgba(168,85,247,.05));">
            <div class="stat-label">Production Orders</div>
            <div class="stat-value">{{ $productionOrdersCount ?? 0 }}</div>
            <div class="stat-note">production stream</div>
        </div>

        <div class="stat-card" style="border-color: rgba(239,68,68,.48); background: linear-gradient(135deg, rgba(239,68,68,.22), rgba(239,68,68,.05));">
            <div class="stat-label">Operational Risk</div>
            <div class="stat-value">{{ $delayedProjectsCount + $endingSoonProjectsCount }}</div>
            <div class="stat-note">risk indicators</div>
        </div>
    </div>

    <div class="stats-grid" style="margin-bottom:8px;">
        @if($delayedProjectsCount > 0)
            <div class="stat-card">
                <div class="stat-label">Delayed Projects</div>
                <div class="stat-value" style="color:#f9a3a3;">{{ $delayedProjectsCount }}</div>
            </div>
        @endif

        @if($endingSoonProjectsCount > 0)
            <div class="stat-card">
                <div class="stat-label">Ending Soon</div>
                <div class="stat-value" style="color:#f6cf7a;">{{ $endingSoonProjectsCount }}</div>
            </div>
        @endif

        @if(auth()->user()->role == 'hr')
            <div class="stat-card">
                <div class="stat-label">Residency Alerts</div>
                <div class="stat-value" style="color:#ccb0ff;">
                    {{ $expiredResidencyEmployees->count() + $residencyExpiringEmployees->count() }}
                </div>
            </div>
        @endif
    </div>

    <div class="details-grid" style="margin-bottom:8px;">
        <div class="detail-box" style="background:linear-gradient(135deg, rgba(59,130,246,.14), rgba(15,23,42,.40));">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:8px;">
                <strong style="font-size:11px; color:#eef5ff;">Execution Pulse</strong>
                <span class="badge badge-blue">Live</span>
            </div>
            <div style="margin-top:6px; font-size:11px; color:#9ec5ff;">
                Factory + installation operations at a glance.
            </div>
            <div class="actions-row" style="margin-top:7px;">
                <a href="{{ route('factory.index') }}" class="btn btn-primary btn-sm">Factory</a>
                <a href="{{ route('installations.index') }}" class="btn btn-success btn-sm">Installation</a>
            </div>
        </div>

        <div class="detail-box" style="background:linear-gradient(135deg, rgba(168,85,247,.14), rgba(15,23,42,.40));">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:8px;">
                <strong style="font-size:11px; color:#f2ecff;">Commercial Snapshot</strong>
                <span class="badge badge-gray">Financial</span>
            </div>
            <div style="margin-top:6px; font-size:11px; color:#ccb0ff;">
                Contracts and procurement alignment.
            </div>
            <div class="actions-row" style="margin-top:7px;">
                <a href="{{ route('sales-contracts.index') }}" class="btn btn-secondary btn-sm">Contracts</a>
                @if(auth()->user()->role == 'admin' || auth()->user()->role == 'manager')
                    <a href="{{ route('purchases.index') }}" class="btn btn-warning btn-sm">Purchases</a>
                @endif
            </div>
        </div>

        <div class="detail-box" style="background:linear-gradient(135deg, rgba(239,68,68,.14), rgba(15,23,42,.40));">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:8px;">
                <strong style="font-size:11px; color:#fff0f0;">Support & Compliance</strong>
                <span class="badge badge-red">Tracked</span>
            </div>
            <div style="margin-top:6px; font-size:11px; color:#f9a3a3;">
                HR, leaves, and governance status.
            </div>
            <div class="actions-row" style="margin-top:7px;">
                <a href="{{ route('leaves.index') }}" class="btn btn-secondary btn-sm">Leaves</a>
                @if(auth()->user()->canViewAuditLogs())
                    <a href="{{ route('audit.index') }}" class="btn btn-danger btn-sm">Audit</a>
                @endif
            </div>
        </div>
    </div>

    {{-- additional analytics row --}}
    <div class="details-grid" style="margin-bottom:8px;">
        <div class="detail-box">
            <strong style="font-size:11px; color:#f7fbff;">Workload Distribution</strong>
            <div style="margin-top:6px;">
                <x-ui.progress :value="min(($architectProjectsCount ?? 0) * 10, 100)" color="#3b82f6" />
            </div>
            <div style="margin-top:4px; font-size:10px; color:#8fa3c1;">Design & planning load indicator</div>
        </div>

        <div class="detail-box">
            <strong style="font-size:11px; color:#f7fbff;">Delivery Readiness</strong>
            <div style="margin-top:6px;">
                <x-ui.progress :value="min(($installationProjectsCount ?? 0) * 10, 100)" color="#22c55e" />
            </div>
            <div style="margin-top:4px; font-size:10px; color:#8fa3c1;">Installation execution readiness</div>
        </div>

        <div class="detail-box">
            <strong style="font-size:11px; color:#f7fbff;">Risk Pressure</strong>
            <div style="margin-top:6px;">
                <x-ui.progress :value="min((($delayedProjectsCount + $endingSoonProjectsCount) ?? 0) * 12, 100)" color="#ef4444" />
            </div>
            <div style="margin-top:4px; font-size:10px; color:#8fa3c1;">Schedule pressure trend</div>
        </div>
    </div>

    @if(auth()->user()->isAdmin())
        <x-ui.card title="Projects Watchlist" subtitle="High-priority portfolio rows">
            <x-ui.table>
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Department</th>
                        <th>Responsible</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Notes</th>
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
                                    <span class="badge badge-red">Delayed</span>
                                @elseif($project->is_ending_soon)
                                    <span class="badge badge-orange">Ending Soon</span>
                                @else
                                    <span class="badge badge-green">On Track</span>
                                @endif
                            </td>
                            <td>{{ $project->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-row">No recent projects</td></tr>
                    @endforelse
                </tbody>
            </x-ui.table>
        </x-ui.card>
    @endif
</x-ui.card>
@endsection
