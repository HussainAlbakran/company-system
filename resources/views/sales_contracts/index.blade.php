@extends('layouts.app')

@section('page_title', 'Contracts')
@section('page_subtitle', 'Financial contracts management')

@section('content')
<x-ui.card title="Contracts Table" subtitle="Show financial contract values only here">
    <div class="actions-row" style="margin-bottom:12px;">
        <a href="{{ route('sales-contracts.create') }}" class="btn btn-primary">+ New Contract</a>
    </div>
    <x-ui.table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Contract No.</th>
                    <th>Client</th>
                    <th>Project</th>
                    <th>Total Value</th>
                    <th>Stage</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contracts as $contract)
                    <tr>
                        <td>{{ $contract->id }}</td>

                        <td>{{ $contract->contract_no }}</td>

                        <td>{{ $contract->client_name }}</td>

                        <td>{{ $contract->project_name }}</td>

                        <td>
                            {{ number_format($contract->project_value ?? 0, 2) }}
                        </td>

                        <td>
                            @if($contract->project)
                                <span class="badge badge-blue">
                                    {{ $contract->project->current_stage }}
                                </span>
                            @else
                                -
                            @endif
                        </td>

                        <td>
                            <span class="badge badge-green">
                                {{ $contract->status }}
                            </span>
                        </td>

                        <td>
                            <div class="actions-row">
                                <a href="{{ route('sales-contracts.show', $contract->id) }}" class="btn btn-sm btn-primary">View</a>
                                <a href="{{ route('sales-contracts.edit', $contract->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('sales-contracts.destroy', $contract->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty-row">No contracts found</td></tr>
                @endforelse
            </tbody>
    </x-ui.table>
    <div style="margin-top:15px;">{{ $contracts->links() }}</div>
</x-ui.card>
@endsection