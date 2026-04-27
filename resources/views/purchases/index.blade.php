@extends('layouts.app')

@section('page_title', __('purchases.page_title'))
@section('page_subtitle', __('purchases.page_subtitle'))

@section('content')
<x-ui.card :title="__('purchases.card_title')" :subtitle="__('purchases.card_subtitle')">
    <div class="actions-row" style="margin-bottom:12px;">
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">+ {{ __('purchases.add_purchase') }}</a>
        <a href="{{ route('purchases.material-requests.index') }}" class="btn btn-secondary" style="display:inline-flex; align-items:center; gap:8px;">
            {{ __('purchases.architect_requests') }}
            <span
                class="badge {{ ($incomingArchitectMaterialRequestsCount ?? 0) > 0 ? 'badge-orange' : 'badge-gray' }}"
                title="{{ __('purchases.architect_requests_badge_title') }}"
                style="min-width:1.75rem; text-align:center; font-weight:700;"
            >{{ (int) ($incomingArchitectMaterialRequestsCount ?? 0) }}</span>
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="page-card" style="margin-bottom:20px;">
        <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap;">

            <select name="project_id" class="form-control">
                <option value="">{{ __('purchases.filter_all_projects') }}</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>

            <button class="btn btn-primary" type="submit">{{ __('purchases.search') }}</button>

        </form>
    </div>

    <div class="form-grid" style="margin-bottom:20px;">

        <div class="detail-box">
            <strong>{{ __('purchases.total_contract_purchases') }}</strong>
            <div class="badge badge-blue">
                {{ number_format($totalContractPurchasesCost, 2) }}
            </div>
        </div>

    </div>

    <x-ui.table>
            <thead>
                <tr>
                    <th>{{ __('purchases.th_number') }}</th>
                    <th>{{ __('purchases.th_project') }}</th>
                    <th>{{ __('purchases.th_line') }}</th>
                    <th>{{ __('purchases.th_quantity') }}</th>

                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'manager')
                        <th>{{ __('purchases.th_cost') }}</th>
                    @endif

                    <th>{{ __('purchases.th_vendor') }}</th>
                    <th>{{ __('purchases.th_date') }}</th>
                    <th>{{ __('purchases.th_action') }}</th>
                </tr>
            </thead>

            <tbody>
                @forelse($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->id }}</td>

                        <td>
                            {{ $purchase->project->name ?? '-' }}
                        </td>

                        <td>
                            <strong>{{ $purchase->title }}</strong><br>
                            <small style="color:#6b7280;">
                                {{ $purchase->description ?? '' }}
                            </small>
                        </td>

                        <td>{{ $purchase->quantity ?? 1 }}</td>

                        @if(auth()->user()->role == 'admin' || auth()->user()->role == 'manager')
                            <td>{{ number_format($purchase->cost, 2) }}</td>
                        @endif

                        <td>{{ $purchase->vendor ?? '-' }}</td>

                        <td>{{ $purchase->purchase_date ?? '-' }}</td>

                        <td style="display:flex; gap:6px;">
                            <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn btn-warning btn-sm">
                                {{ __('purchases.edit') }}
                            </a>

                            <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" onsubmit="return confirm(@json(__('purchases.confirm_delete')))">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm" type="submit">
                                    {{ __('purchases.delete') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ (auth()->user()->role == 'admin' || auth()->user()->role == 'manager') ? 8 : 7 }}" class="empty-row">
                            {{ __('purchases.empty') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
    </x-ui.table>

    @if(method_exists($purchases, 'links'))
        <div style="margin-top:16px;">
            {{ $purchases->links() }}
        </div>
    @endif

</x-ui.card>

@endsection
