@extends('layouts.app')

@section('page_title', __('custody_settlement.index_title'))
@section('page_subtitle', __('custody_settlement.index_subtitle'))

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px;">
        <div>
            <h1 class="page-title">{{ __('custody_settlement.index_title') }}</h1>
            <p style="margin:6px 0 0; color:#6b7280;">{{ __('custody_settlement.index_subtitle') }}</p>
        </div>
        <a href="{{ route('cash-flow.index') }}" class="btn btn-secondary btn-sm">{{ __('financial_custody.back_cash_flow') }}</a>
    </div>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-danger">{{ session('error') }}</div>@endif

    <div class="table-wrap" style="margin-top:16px;">
        <table>
            <thead>
                <tr>
                    <th>{{ __('custody_settlement.employee') }}</th>
                    <th>{{ __('financial_custody.amount_issued') }}</th>
                    <th>{{ __('financial_custody.amount_remaining') }}</th>
                    <th>{{ __('financial_custody.issued_at') }}</th>
                    <th>{{ __('custody_settlement.th_action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($custodies as $custody)
                    <tr>
                        <td>{{ $custody->employee->name ?? '-' }}</td>
                        <td>{{ number_format((float)$custody->amount_issued, 2) }}</td>
                        <td>{{ number_format((float)$custody->amount_remaining, 2) }}</td>
                        <td>{{ $custody->issued_at?->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('custody-settlements.open', $custody) }}" class="btn btn-primary btn-sm">{{ __('custody_settlement.btn_open') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">{{ __('custody_settlement.no_open_custodies') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
