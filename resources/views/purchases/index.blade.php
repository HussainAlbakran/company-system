@extends('layouts.app')

@section('page_title', 'Contract Purchases')
@section('page_subtitle', 'Project-related procurement and costs')

@section('content')
<x-ui.card title="Contract Purchases" subtitle="Operational purchases linked to project contracts">
    <div class="actions-row" style="margin-bottom:12px;">
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">+ Add Purchase</a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- الفلاتر --}}
    <div class="page-card" style="margin-bottom:20px;">
        <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap;">

            <select name="project_id" class="form-control">
                <option value="">كل المشاريع</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>

            <button class="btn btn-primary" type="submit">Search</button>

        </form>
    </div>

    {{-- الإجمالي --}}
    <div class="form-grid" style="margin-bottom:20px;">

        <div class="detail-box">
            <strong>Total Contract Purchases</strong>
            <div class="badge badge-blue">
                {{ number_format($totalContractPurchasesCost, 2) }}
            </div>
        </div>

    </div>

    <x-ui.table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Project</th>
                    <th>Item</th>
                    <th>Qty</th>

                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'manager')
                        <th>Cost</th>
                    @endif

                    <th>Vendor</th>
                    <th>Date</th>
                    <th>Action</th>
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
                                Edit
                            </a>

                            <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
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
                            لا توجد بيانات
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