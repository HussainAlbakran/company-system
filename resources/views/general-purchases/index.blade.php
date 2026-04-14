@extends('layouts.app')

@section('page_title', 'General Purchases')
@section('page_subtitle', 'Assets and maintenance expenses')

@section('content')
<x-ui.card title="General Purchases" subtitle="Asset purchasing and maintenance operations">
    <div class="actions-row" style="margin-bottom:12px;">
        <a href="{{ route('general-purchases.create') }}" class="btn btn-primary">+ Add New</a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- الفلترة --}}
    <div class="page-card" style="margin-bottom:20px;">
        <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap;">

            <select name="type" class="form-control">
                <option value="">كل الأنواع</option>
                <option value="asset_purchase" {{ request('type') == 'asset_purchase' ? 'selected' : '' }}>
                    شراء أصول
                </option>
                <option value="general_maintenance" {{ request('type') == 'general_maintenance' ? 'selected' : '' }}>
                    صيانة عامة
                </option>
            </select>

            <button class="btn btn-primary">Search</button>

        </form>
    </div>

    {{-- الإجماليات --}}
    <div class="form-grid" style="margin-bottom:20px;">

        <div class="detail-box">
            <strong>Total Asset Purchases</strong>
            <div class="badge badge-green">
                {{ number_format($totalAssetPurchaseCost, 2) }}
            </div>
        </div>

        <div class="detail-box">
            <strong>Total Maintenance</strong>
            <div class="badge badge-orange">
                {{ number_format($totalGeneralMaintenanceCost, 2) }}
            </div>
        </div>

        <div class="detail-box">
            <strong>Grand Total</strong>
            <div class="badge badge-blue">
                {{ number_format($totalGeneralPurchasesCost, 2) }}
            </div>
        </div>

    </div>

    {{-- الجدول --}}
    <x-ui.table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Item</th>
                    <th>Qty</th>

                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'manager')
                        <th>Cost</th>
                    @endif

                    <th>Vendor</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($purchases as $purchase)
                    <tr>
                        <td>{{ $purchase->id }}</td>

                        <td>
                            @if($purchase->type == 'asset_purchase')
                                <span class="badge badge-green">شراء أصول</span>
                            @else
                                <span class="badge badge-orange">صيانة عامة</span>
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
                                Edit
                            </a>

                            <form action="{{ route('general-purchases.destroy', $purchase->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm" type="submit">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ (auth()->user()->role == 'admin' || auth()->user()->role == 'manager') ? 8 : 7 }}" class="empty-row">
                            لا توجد بيانات حالياً
                        </td>
                    </tr>
                @endforelse
            </tbody>
    </x-ui.table>

    {{-- pagination --}}
    <div style="margin-top:16px;">
        {{ $purchases->links() }}
    </div>

</x-ui.card>

@endsection