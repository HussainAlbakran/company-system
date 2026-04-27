@extends('layouts.app')

@section('page_title', __('projects.page_title'))
@section('page_subtitle', __('projects.page_subtitle'))

@section('content')
@php
    $u = auth()->user();
    $showProjectValue = $u->canViewProjectFinancials() || $u->canViewProjectValueOnly();
    $showProjectExpenses = $u->canViewProjectFinancials();
    $tableColCount = 6 + ($showProjectValue ? 1 : 0) + ($showProjectExpenses ? 1 : 0);
@endphp
<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 class="page-title">{{ __('projects.page_title') }}</h1>
            <p>{{ __('projects.page_subtitle') }}</p>
        </div>

        <a href="{{ route('engineering-projects.create') }}" class="btn btn-primary">
            ➕ {{ __('projects.add_project') }}
        </a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>{{ __('projects.th_project') }}</th>
                    <th>{{ __('projects.th_department') }}</th>
                    <th>{{ __('projects.th_responsible') }}</th>
                    <th>{{ __('projects.th_status') }}</th>
                    <th>{{ __('projects.th_progress') }}</th>
                    @if($showProjectValue)
                    <th>{{ __('projects.th_value') }}</th>
                    @endif
                    @if($showProjectExpenses)
                    <th>{{ __('projects.th_expenses') }}</th>
                    @endif
                    <th>{{ __('projects.th_actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @forelse($projects as $project)
                <tr>

                    <td>
                        <a href="{{ route('engineering-projects.show', $project->id) }}">
                            {{ $project->name }}
                        </a>
                    </td>

                    <td>{{ $project->department->name ?? '-' }}</td>

                    <td>{{ $project->responsibleEmployee->name ?? '-' }}</td>

                    <td>{{ $project->status ?? '-' }}</td>

                    <td>
                        <span class="badge badge-blue">
                            {{ $project->progress_percentage }}%
                        </span>
                    </td>

                    @if($showProjectValue)
                    <td>{{ number_format($project->project_value, 2) }}</td>
                    @endif

                    @if($showProjectExpenses)
                    <td>{{ number_format($project->expenses, 2) }}</td>
                    @endif

                    <td style="display:flex; gap:6px;">
                        <a href="{{ route('engineering-projects.show', $project->id) }}" class="btn btn-sm btn-blue">{{ __('common.view') }}</a>

                        <a href="{{ route('engineering-projects.edit', $project->id) }}" class="btn btn-sm btn-orange">{{ __('common.edit') }}</a>

                        <form action="{{ route('engineering-projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm(@json(__('projects.confirm_delete')))">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-red">{{ __('common.delete') }}</button>
                        </form>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="{{ $tableColCount }}" class="empty-row">
                        {{ __('projects.empty') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($projects, 'links'))
        <div style="margin-top:16px;">
            {{ $projects->links() }}
        </div>
    @endif

</div>
@endsection
