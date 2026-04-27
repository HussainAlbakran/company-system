@extends('layouts.app')

@section('page_title', __('architect.tasks_page_title'))
@section('page_subtitle', __('architect.tasks_page_subtitle'))

@section('content')
<x-ui.card :title="__('architect.tasks_card_title')" :subtitle="__('architect.tasks_card_subtitle')">
    <x-ui.table>
            <thead>
                <tr>
                    <th>{{ __('architect.th_project_code') }}</th>
                    <th>{{ __('architect.th_project_name') }}</th>
                    <th>{{ __('architect.th_client') }}</th>
                    <th>{{ __('architect.th_stage_short') }}</th>
                    <th>{{ __('architect.open') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                    <tr>
                        <td>{{ $project->project_code }}</td>
                        <td>{{ $project->name }}</td>
                        <td>{{ $project->client_name }}</td>
                        <td>
                            <span class="badge badge-blue">
                                {{ $project->current_stage }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('architect-tasks.show', $project->id) }}" class="btn btn-primary btn-sm">
                                {{ __('architect.open') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-row">{{ __('architect.tasks_empty') }}</td></tr>
                @endforelse
            </tbody>
    </x-ui.table>
</x-ui.card>
@endsection