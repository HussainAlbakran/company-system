@extends('layouts.app')

@section('page_title', __('project_reports.board_title'))
@section('page_subtitle', __('project_reports.board_subtitle'))

@section('content')
<div class="dashboard-stack">
    <section class="dashboard-panel">
        <div class="panel-head" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
            <div>
                <h2 class="panel-title">{{ __('project_reports.board_title') }}</h2>
                <p class="panel-subtitle">{{ __('project_reports.board_subtitle') }}</p>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('project-reports.archive') }}" class="btn btn-secondary btn-sm">
                    {{ __('project_reports.archive_btn') }}
                </a>
                @if(auth()->user()->canSubmitProjectReports())
                    <a href="{{ route('project-reports.create') }}" class="btn btn-primary btn-sm">
                        {{ __('project_reports.register_btn') }}
                    </a>
                @endif
            </div>
        </div>

        <form method="GET" action="{{ route('project-reports.board') }}" style="margin-top:12px;">
            <div class="form-grid" style="align-items:end;">
                <div class="form-group">
                    <label>{{ __('project_reports.board_search') }}</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('project_reports.board_search_placeholder') }}">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{{ __('common.search') }}</button>
                    <a href="{{ route('project-reports.board') }}" class="btn btn-secondary">{{ __('common.reset') }}</a>
                </div>
            </div>
        </form>

        <div class="stats-grid" style="margin-top:18px;">
            @forelse($projects as $project)
                <div class="stat-card" style="display:flex; flex-direction:column; gap:10px; min-height:160px;">
                    <div>
                        <div class="stat-label">{{ $project->project_code ?: __('project_reports.card_code') }}</div>
                        <div class="stat-value" style="font-size:18px; color:#000;">{{ $project->name }}</div>
                    </div>
                    <div style="font-size:12px; color:#111827; line-height:1.7;">
                        <div><strong>{{ __('project_reports.card_client') }}:</strong> {{ $project->client_name ?: '-' }}</div>
                        <div><strong>{{ __('project_reports.card_status') }}:</strong> {{ $project->status ?: '-' }}</div>
                        <div><strong>{{ __('project_reports.card_reports_count', ['count' => $project->reports_count]) }}</strong></div>
                    </div>
                    <div style="margin-top:auto;">
                        <a href="{{ route('project-reports.show', $project) }}" class="btn btn-success btn-sm">
                            {{ __('project_reports.card_view') }}
                        </a>
                    </div>
                </div>
            @empty
                <div class="stat-card" style="grid-column:1 / -1;">
                    <div class="empty-row">{{ __('project_reports.board_empty') }}</div>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
