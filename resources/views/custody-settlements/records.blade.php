@extends('layouts.app')

@section('page_title', __('custody_settlement.records_title'))
@section('page_subtitle', __('custody_settlement.records_subtitle'))

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px;">
        <div>
            <h1 class="page-title">{{ __('custody_settlement.records_title') }}</h1>
            <p style="margin:6px 0 0; color:#6b7280;">{{ __('custody_settlement.records_subtitle') }}</p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('custody-settlements.index') }}" class="btn btn-secondary btn-sm">{{ __('navigation.custody_settlement') }}</a>
            <a href="{{ route('cash-flow.index') }}" class="btn btn-secondary btn-sm">{{ __('financial_custody.back_cash_flow') }}</a>
        </div>
    </div>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-danger">{{ session('error') }}</div>@endif

    <form method="get" class="form-grid" style="align-items:end; margin:16px 0; gap:12px;">
        <div class="form-group" style="margin:0;">
            <label for="employee">{{ __('custody_settlement.search_employee') }}</label>
            <input type="text" id="employee" name="employee" value="{{ $filters['employee'] ?? '' }}" maxlength="120" placeholder="{{ __('custody_settlement.search_employee_placeholder') }}">
        </div>
        <div class="form-group" style="margin:0;">
            <label for="code">{{ __('custody_settlement.search_code') }}</label>
            <input type="text" id="code" name="code" value="{{ $filters['code'] ?? '' }}" maxlength="20" dir="ltr" placeholder="26001" style="text-align:left;">
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary btn-sm">{{ __('custody_settlement.search_btn') }}</button>
            @if(($filters['employee'] ?? '') !== '' || ($filters['code'] ?? '') !== '')
                <a href="{{ route('custody-settlements.records') }}" class="btn btn-secondary btn-sm">{{ __('custody_settlement.search_reset') }}</a>
            @endif
        </div>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>{{ __('custody_settlement.employee') }}</th>
                    <th>{{ __('custody_settlement.col_settlement_code') }}</th>
                    <th>{{ __('custody_settlement.col_amount_spent') }}</th>
                    <th>{{ __('custody_settlement.approved_at') }}</th>
                    <th>{{ __('custody_settlement.th_action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($settlements as $settlement)
                    <tr>
                        <td>{{ $settlement->employee->name ?? '—' }}</td>
                        <td dir="ltr" style="font-weight:700;">{{ $settlement->referenceCode() }}</td>
                        <td style="font-weight:700; color:#b91c1c;">{{ number_format((float) $settlement->grand_total, 2) }}</td>
                        <td>{{ $settlement->approved_at?->format('Y-m-d') ?? ($settlement->settlement_date?->format('Y-m-d') ?? '—') }}</td>
                        <td>
                            <a href="{{ route('custody-settlements.show', $settlement) }}" class="btn btn-primary btn-sm">{{ __('custody_settlement.btn_open') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center; color:#6b7280; padding:24px;">{{ __('custody_settlement.records_empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($settlements->hasPages())
        <div style="margin-top:16px;">{{ $settlements->links() }}</div>
    @endif
</div>
@endsection
