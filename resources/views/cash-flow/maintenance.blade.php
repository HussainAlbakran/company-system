@extends('layouts.app')

@section('page_title', __('cash_flow.maintenance_page_title'))
@section('page_subtitle', __('cash_flow.maintenance_page_subtitle'))

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 class="page-title">{{ __('cash_flow.maintenance_page_title') }}</h1>
            <p style="color:#6b7280; margin:8px 0 0;">{{ __('cash_flow.maintenance_page_subtitle') }}</p>
        </div>
        <a href="{{ route('cash-flow.index') }}" class="btn btn-secondary">{{ __('cash_flow.back_to_cash_flow') }}</a>
    </div>

    <div class="detail-box" style="margin:16px 0; max-width:320px;">
        <strong>{{ __('cash_flow.maintenance_total') }}</strong>
        <div style="color:#b91c1c; font-weight:800; font-size:20px; margin-top:6px;">-{{ number_format($totalMaintenance, 2) }}</div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('cash_flow.field_date') }}</th>
                    <th>{{ __('cash_flow.maintenance_asset') }}</th>
                    <th>{{ __('cash_flow.th_serial') }}</th>
                    <th>{{ __('cash_flow.field_amount') }}</th>
                    <th>{{ __('cash_flow.field_notes') }}</th>
                    <th>{{ __('cash_flow.recorded_by') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>{{ $log->maintenance_date?->format('Y-m-d') }}</td>
                        <td>
                            @if($log->asset)
                                <a href="{{ route('assets.show', $log->asset) }}" class="employee-link">{{ $log->asset_name }}</a>
                            @else
                                {{ $log->asset_name }}
                            @endif
                        </td>
                        <td>{{ $log->serial_number ?? '-' }}</td>
                        <td class="amount-expense" style="color:#b91c1c; font-weight:700;">-{{ number_format((float) $log->maintenance_cost, 2) }}</td>
                        <td>{{ $log->notes ?? '-' }}</td>
                        <td>{{ $log->recorder->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-row">{{ __('cash_flow.maintenance_empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($logs, 'links'))
        <div style="margin-top:16px;">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
