@extends('layouts.app')

@section('page_title', __('contracts.page_title'))
@section('page_subtitle', __('contracts.page_subtitle'))

@section('content')
@php
    $u = auth()->user();
    $showContractValue = $u->canViewProjectFinancials() || $u->canViewProjectValueOnly();
    $contractsTableColspan = 7 + ($showContractValue ? 1 : 0);
@endphp
<x-ui.card :title="__('contracts.card_title')" :subtitle="__('contracts.card_subtitle')">
    <div class="actions-row" style="margin-bottom:12px;">
        <a href="{{ route('sales-contracts.create') }}" class="btn btn-primary">+ {{ __('contracts.new_contract') }}</a>
    </div>
    <x-ui.table>
            <thead>
                <tr>
                    <th>{{ __('contracts.th_id') }}</th>
                    <th>{{ __('contracts.th_contract_no') }}</th>
                    <th>{{ __('contracts.th_client') }}</th>
                    <th>{{ __('contracts.th_project') }}</th>
                    @if($showContractValue)
                    <th>{{ __('contracts.th_total_value') }}</th>
                    @endif
                    <th>{{ __('contracts.th_stage') }}</th>
                    <th>{{ __('contracts.th_status') }}</th>
                    <th>{{ __('contracts.th_actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contracts as $contract)
                    <tr>
                        <td>{{ $contract->id }}</td>

                        <td>{{ $contract->contract_no }}</td>

                        <td>{{ $contract->client_name }}</td>

                        <td>{{ $contract->project_name }}</td>

                        @if($showContractValue)
                        <td>
                            {{ number_format($contract->project_value ?? 0, 2) }}
                        </td>
                        @endif

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
                                <a href="{{ route('sales-contracts.show', $contract->id) }}" class="btn btn-sm btn-primary">{{ __('contracts.view') }}</a>
                                <a href="{{ route('sales-contracts.edit', $contract->id) }}" class="btn btn-sm btn-warning">{{ __('contracts.edit') }}</a>
                                <form action="{{ route('sales-contracts.destroy', $contract->id) }}" method="POST" onsubmit="return confirm(@json(__('contracts.confirm_delete')))">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">{{ __('contracts.delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ $contractsTableColspan }}" class="empty-row">{{ __('contracts.empty') }}</td></tr>
                @endforelse
            </tbody>
    </x-ui.table>
    <div style="margin-top:15px;">{{ $contracts->links() }}</div>
</x-ui.card>
@endsection
