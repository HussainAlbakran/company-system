@extends('layouts.app')

@section('page_title', 'Design')
@section('page_subtitle', 'Architect stage projects')

@section('content')
<x-ui.card title="Design Projects" subtitle="Measurements and technical planning">
    <x-ui.table>
            <thead>
                <tr>
                    <th>Project Code</th>
                    <th>Project Name</th>
                    <th>Client</th>
                    <th>Stage</th>
                    <th>Open</th>
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
                                Open
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-row">No design projects</td></tr>
                @endforelse
            </tbody>
    </x-ui.table>
</x-ui.card>
@endsection