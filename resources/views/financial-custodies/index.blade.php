@extends('layouts.app')

@section('page_title', __('financial_custody.page_title'))
@section('page_subtitle', __('financial_custody.page_subtitle'))

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px;">
        <div>
            <h1 class="page-title">{{ __('financial_custody.page_title') }}</h1>
            <p style="margin:6px 0 0; color:#6b7280;">{{ __('financial_custody.page_subtitle') }}</p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="{{ route('financial-custodies.create') }}" class="btn btn-primary btn-sm">{{ __('cash_flow.btn_issue_custody') }}</a>
            <a href="{{ route('cash-flow.index') }}" class="btn btn-secondary btn-sm">{{ __('financial_custody.back_cash_flow') }}</a>
        </div>
    </div>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-danger">{{ session('error') }}</div>@endif

    <form method="get" style="display:flex; gap:10px; flex-wrap:wrap; margin:16px 0;">
        <select name="status">
            <option value="">{{ __('financial_custody.filter_all') }}</option>
            <option value="open" @selected(request('status')==='open')>{{ __('financial_custody.status_open') }}</option>
            <option value="closed" @selected(request('status')==='closed')>{{ __('financial_custody.status_closed') }}</option>
        </select>
        <select name="employee_id">
            <option value="">{{ __('financial_custody.employee') }}</option>
            @foreach($employees as $emp)
                <option value="{{ $emp->id }}" @selected((string) request('employee_id') === (string) $emp->id)>{{ $emp->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-secondary btn-sm">{{ __('cash_flow.apply_filter') }}</button>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>{{ __('financial_custody.employee') }}</th>
                    <th>{{ __('financial_custody.amount_issued') }}</th>
                    <th>{{ __('financial_custody.total_spent') }}</th>
                    <th>{{ __('financial_custody.amount_remaining') }}</th>
                    <th>{{ __('financial_custody.issued_at') }}</th>
                    <th>{{ __('financial_custody.status') }}</th>
                    <th>{{ __('financial_custody.th_action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($custodies as $custody)
                    <tr>
                        <td>
                            <a href="{{ route('financial-custodies.show', $custody) }}" style="font-weight:600; color:inherit;">
                                {{ $custody->employee->name ?? '-' }}
                            </a>
                        </td>
                        <td>{{ number_format((float) $custody->amount_issued, 2) }}</td>
                        <td>{{ number_format($custody->totalSpent(), 2) }}</td>
                        <td>{{ number_format((float) $custody->amount_remaining, 2) }}</td>
                        <td>{{ $custody->issued_at?->format('Y-m-d') }}</td>
                        <td>
                            @if($custody->isOpen())
                                <span class="badge badge-orange">{{ __('financial_custody.status_open') }}</span>
                            @else
                                <span class="badge badge-green">{{ __('financial_custody.status_closed') }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('financial-custodies.show', $custody) }}" class="btn btn-primary btn-sm">{{ __('financial_custody.btn_open') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">{{ __('financial_custody.empty') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $custodies->links() }}
</div>
@endsection
