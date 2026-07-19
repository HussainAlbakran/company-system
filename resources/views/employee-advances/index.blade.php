@extends('layouts.app')

@section('page_title', __('employee_advance.page_title'))
@section('page_subtitle', __('employee_advance.page_subtitle'))

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px;">
        <h1 class="page-title">{{ __('employee_advance.page_title') }}</h1>
        <div style="display:flex; gap:8px;">
            <a href="{{ route('employee-advances.create') }}" class="btn btn-warning btn-sm">{{ __('cash_flow.btn_issue_advance') }}</a>
            <a href="{{ route('cash-flow.index') }}" class="btn btn-secondary btn-sm">{{ __('financial_custody.back_cash_flow') }}</a>
        </div>
    </div>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>{{ __('employee_advance.employee') }}</th>
                    <th>{{ __('employee_advance.issued_at') }}</th>
                    <th>{{ __('employee_advance.repayment_start') }}</th>
                    <th>{{ __('employee_advance.total_amount') }}</th>
                    <th>{{ __('employee_advance.installment_count') }}</th>
                    <th>{{ __('employee_advance.installment_amount') }}</th>
                    <th>{{ __('employee_advance.installments_paid') }}</th>
                    <th>{{ __('employee_advance.remaining_balance') }}</th>
                    <th>{{ __('employee_advance.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($advances as $advance)
                    <tr>
                        <td>{{ $advance->employee->name ?? '-' }}</td>
                        <td>{{ $advance->issued_at?->format('Y-m-d') ?? '-' }}</td>
                        <td>
                            {{ $advance->repaymentStartLabel() }}
                            @if((int) ($advance->repayment_delay_months ?? 0) > 0)
                                <span style="font-size:11px; color:#6b7280; display:block;">
                                    {{ __('employee_advance.repayment_after_months', ['months' => (int) $advance->repayment_delay_months]) }}
                                </span>
                            @endif
                        </td>
                        <td>{{ number_format((float)$advance->total_amount, 2) }}</td>
                        <td>{{ $advance->installment_count }}</td>
                        <td>{{ number_format((float)$advance->installment_amount, 2) }}</td>
                        <td>{{ $advance->installments_paid }} / {{ $advance->installment_count }}</td>
                        <td>{{ number_format($advance->remainingBalance(), 2) }}</td>
                        <td>{{ $advance->isActive() ? __('employee_advance.status_active') : __('employee_advance.status_completed') }}</td>
                    </tr>
                    @if($advance->payments->isNotEmpty())
                        <tr>
                            <td colspan="9" style="padding:0; background:#f9fafb;">
                                <div style="padding:12px 16px;">
                                    <div style="font-weight:600; margin-bottom:8px;">{{ __('employee_advance.payments_title') }}</div>
                                    <div class="table-wrap">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>{{ __('employee_advance.th_payroll_register') }}</th>
                                                    <th>{{ __('employee_advance.th_amount') }}</th>
                                                    <th>{{ __('employee_advance.th_recorded_at') }}</th>
                                                    <th>{{ __('employee_advance.th_recorded_by') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($advance->payments as $payment)
                                                    <tr>
                                                        <td>
                                                            @if($payment->payrollRegister && auth()->user()?->canManageEmployees())
                                                                <a href="{{ route('employees.payroll-register.show', $payment->payrollRegister) }}">
                                                                    {{ __('employee_advance.payroll_register_link', ['period' => $payment->payrollRegister->periodLabel()]) }}
                                                                </a>
                                                            @elseif($payment->payrollRegister)
                                                                {{ $payment->payrollRegister->periodLabel() }}
                                                            @else
                                                                {{ $payment->month }}/{{ $payment->year }}
                                                            @endif
                                                        </td>
                                                        <td>{{ number_format((float) $payment->amount, 2) }}</td>
                                                        <td>{{ $payment->recorded_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                                        <td>{{ $payment->recorder->name ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="9">{{ __('employee_advance.empty') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $advances->links() }}
</div>
@endsection
