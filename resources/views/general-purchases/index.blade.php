@extends('layouts.app')

@section('page_title', __('general_purchases.page_title'))
@section('page_subtitle', __('general_purchases.page_subtitle'))

@section('content')
<x-ui.card :title="__('general_purchases.card_title')" :subtitle="__('general_purchases.card_subtitle')">
    <div class="actions-row" style="margin-bottom:12px; flex-wrap:wrap; gap:8px;">
        <a href="{{ route('general-purchases.create') }}" class="btn btn-primary">+ {{ __('general_purchases.add_new') }}</a>
        <a href="{{ route('assets.registration-expiring-soon') }}" class="btn btn-warning btn-sm">{{ __('general_purchases.vehicle_expiry_link') }}</a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="page-card" style="margin-bottom:20px;">
        <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap;">

            <select name="type" class="form-control">
                <option value="">{{ __('general_purchases.filter_all_types') }}</option>
                <option value="asset_purchase" {{ request('type') == 'asset_purchase' ? 'selected' : '' }}>
                    {{ __('general_purchases.type_asset_purchase') }}
                </option>
                <option value="general_maintenance" {{ request('type') == 'general_maintenance' ? 'selected' : '' }}>
                    {{ __('general_purchases.type_general_maintenance') }}
                </option>
            </select>

            <button class="btn btn-primary">{{ __('general_purchases.search') }}</button>

        </form>
    </div>

    <div class="form-grid" style="margin-bottom:20px;">

        <div class="detail-box">
            <strong>{{ __('general_purchases.total_asset_purchases') }}</strong>
            <div class="badge badge-green">
                {{ number_format($totalAssetPurchaseCost, 2) }}
            </div>
        </div>

        <div class="detail-box">
            <strong>{{ __('general_purchases.total_maintenance') }}</strong>
            <div class="badge badge-orange">
                {{ number_format($totalGeneralMaintenanceCost, 2) }}
            </div>
        </div>

        <div class="detail-box">
            <strong>{{ __('general_purchases.grand_total') }}</strong>
            <div class="badge badge-blue">
                {{ number_format($totalGeneralPurchasesCost, 2) }}
            </div>
        </div>

    </div>

    <x-ui.table>
            <thead>
                <tr>
                    <th>{{ __('general_purchases.th_number') }}</th>
                    <th>{{ __('general_purchases.th_type') }}</th>
                    <th>{{ __('general_purchases.th_line') }}</th>
                    <th>{{ __('general_purchases.th_quantity') }}</th>

                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'manager')
                        <th>{{ __('general_purchases.th_cost') }}</th>
                    @endif

                    <th>{{ __('general_purchases.th_vendor') }}</th>
                    <th>{{ __('general_purchases.th_date') }}</th>
                    <th>{{ __('general_purchases.th_actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @forelse($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->id }}</td>

                        <td>
                            @if($purchase->type == 'asset_purchase')
                                <span class="badge badge-green">{{ __('general_purchases.type_asset_purchase') }}</span>
                            @else
                                <span class="badge badge-orange">{{ __('general_purchases.type_general_maintenance') }}</span>
                            @endif
                        </td>

                        <td>
                            <strong>{{ $purchase->title }}</strong>
                            @if($purchase->description)
                                <br>
                                <small style="color:#6b7280;">{{ $purchase->description }}</small>
                            @endif
                        </td>

                        <td>{{ $purchase->quantity ?? 1 }}</td>

                        @if(auth()->user()->role == 'admin' || auth()->user()->role == 'manager')
                            <td>{{ number_format($purchase->cost, 2) }}</td>
                        @endif

                        <td>{{ $purchase->vendor ?? '-' }}</td>

                        <td>{{ $purchase->purchase_date ?? '-' }}</td>

                        <td style="display:flex; gap:6px;">
                            <a href="{{ route('general-purchases.edit', $purchase->id) }}"
                               class="btn btn-warning btn-sm">
                                {{ __('general_purchases.edit') }}
                            </a>

                            <form action="{{ route('general-purchases.destroy', $purchase->id) }}"
                                  method="POST"
                                  onsubmit="return confirm(@json(__('general_purchases.confirm_delete')))">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm" type="submit">
                                    {{ __('general_purchases.delete') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ (auth()->user()->role == 'admin' || auth()->user()->role == 'manager') ? 8 : 7 }}" class="empty-row">
                            {{ __('general_purchases.empty') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
    </x-ui.table>

    <div style="margin-top:16px;">
        {{ $purchases->links() }}
    </div>

</x-ui.card>

@endsection
