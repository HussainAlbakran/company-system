@extends('layouts.app')

@section('page_title', __('architect.index_title'))
@section('page_subtitle', __('architect.index_subtitle'))

@section('content')

<div class="page-card">

    <div class="page-header">
        <h2>{{ __('architect.index_title') }}</h2>
        <p>{{ __('architect.index_subtitle') }}</p>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>{{ __('architect.th_project_code') }}</th>
                    <th>{{ __('architect.th_project_name') }}</th>
                    <th>{{ __('architect.th_client') }}</th>
                    <th>{{ __('architect.th_stage') }}</th>
                    <th>{{ __('architect.th_action') }}</th>
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
                            <div class="actions-row" style="flex-wrap:wrap; gap:8px;">
                                <a href="{{ route('architect.project-material-requirements', $project) }}" class="btn btn-primary">
                                    {{ __('architect.open') }}
                                </a>
                                <form action="{{ route('architect.complete', $project->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        {{ __('architect.complete_send_purchasing') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-row">
                            {{ __('architect.empty_projects') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection