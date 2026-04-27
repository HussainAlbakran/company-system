@extends('layouts.app')

@section('page_title', __('factory.installations_index_title'))
@section('page_subtitle', __('factory.installations_index_subtitle'))

@section('content')

<x-ui.card :title="__('factory.installations_card_title')" :subtitle="__('factory.installations_card_subtitle')">
    <x-ui.table>
            <thead>
                <tr>
                    <th>{{ __('factory.project_code') }}</th>
                    <th>{{ __('factory.project_name') }}</th>
                    <th>{{ __('factory.client') }}</th>
                    <th>{{ __('factory.th_drawing_file') }}</th>
                    <th>{{ __('factory.th_planning_file') }}</th>
                    <th>{{ __('factory.th_measurements_count') }}</th>
                    <th>{{ __('factory.th_planned') }}</th>
                    <th>{{ __('factory.th_produced_short') }}</th>
                    <th>{{ __('factory.th_remaining_short') }}</th>
                    <th>{{ __('factory.th_completion') }}</th>
                    <th>{{ __('factory.th_status') }}</th>
                    <th>{{ __('factory.th_open_project') }}</th>
                    <th>{{ __('factory.th_factory_request') }}</th>
                </tr>
            </thead>

            <tbody>
                @forelse($projects as $project)
                    <tr>
                        <td>{{ $project->project_code }}</td>
                        <td><strong>{{ $project->name }}</strong></td>
                        <td>{{ $project->client_name }}</td>

                        <td>
                            @if($project->architectTask && $project->architectTask->drawing_file)
                                <span class="badge badge-green">{{ __('factory.file_present') }}</span>
                            @else
                                <span class="badge badge-gray">{{ __('factory.file_not_uploaded') }}</span>
                            @endif
                        </td>

                        <td>
                            @if($project->architectTask && $project->architectTask->planning_file)
                                <span class="badge badge-green">{{ __('factory.file_present') }}</span>
                            @else
                                <span class="badge badge-gray">{{ __('factory.file_not_uploaded') }}</span>
                            @endif
                        </td>

                        <td>
                            <span class="badge badge-blue">
                                {{ $project->measurements_count ?? 0 }}
                            </span>
                        </td>

                        <td>{{ number_format($project->planned_quantity ?? 0, 2) }}</td>

                        <td>
                            <span class="badge badge-green">
                                {{ number_format($project->produced_quantity ?? 0, 2) }}
                            </span>
                        </td>

                        <td>
                            @if(($project->remaining_quantity ?? 0) > 0)
                                <span class="badge badge-orange">
                                    {{ number_format($project->remaining_quantity, 2) }}
                                </span>
                            @else
                                <span class="badge badge-green">{{ __('factory.status_completed') }}</span>
                            @endif
                        </td>

                        <td>
                            <x-ui.progress :value="$project->progress_percentage ?? 0" />
                        </td>

                        <td>
                            <span class="badge badge-blue">{{ $project->current_stage }}</span>
                        </td>

                        <td>
                            <a href="{{ route('installations.show', $project->id) }}" class="btn btn-primary btn-sm">{{ __('factory.th_open_project') }}</a>
                        </td>
                        <td>
                            <a href="{{ route('installations.factory-requests.create', $project->id) }}" class="btn btn-secondary btn-sm">{{ __('factory.request_from_factory') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="empty-row">
                            {{ __('factory.installations_empty') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
    </x-ui.table>
</x-ui.card>

@endsection