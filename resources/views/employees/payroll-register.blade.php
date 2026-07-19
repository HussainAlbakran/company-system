@extends('layouts.app')

@section('page_title', __('employees.payroll_page_title'))
@section('page_subtitle', __('employees.payroll_page_subtitle', ['period' => $payrollRegister->month.'/'.$payrollRegister->year]))

@section('content')
<style>
    .payroll-page-shell {
        padding: 0;
    }

    .payroll-actions {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .print-area {
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
    }

    .payroll-paper {
        background: #fff;
        color: #000;
        border: 1px solid #000;
        width: 100%;
        margin: 0;
        padding: 16px;
        box-sizing: border-box;
        position: relative;
    }

    .paper-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        border-bottom: 1px solid #000;
        padding-bottom: 10px;
        margin-bottom: 12px;
    }

    .paper-header-logo {
        width: 96px;
        min-width: 96px;
        flex-shrink: 0;
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
    }

    .company-print-logo {
        width: 88px;
        height: 88px;
        max-width: 100%;
        object-fit: contain;
        display: block;
    }

    .paper-header-center {
        text-align: center;
        flex: 1;
    }

    .paper-header-center h1,
    .paper-header-center h2,
    .paper-header-center p {
        margin: 0;
        line-height: 1.6;
        color: #000;
    }

    .paper-header-center h1 {
        font-size: 22px;
        font-weight: 700;
    }

    .paper-header-center h2 {
        font-size: 19px;
        font-weight: 700;
    }

    .paper-header-right {
        min-width: 170px;
        text-align: right;
        font-weight: 700;
        color: #000;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        margin-bottom: 12px;
    }

    table th, table td {
        border: 1px solid #000;
        padding: 8px;
        text-align: center;
        background: #fff;
        color: #000;
    }

    table th {
        font-weight: bold;
        background: #f5f5f5;
    }

    table td.name-col {
        text-align: right;
        font-weight: 600;
    }

    .summary-box {
        border: 1px solid #000;
        padding: 10px;
        width: 300px;
        background: #fff;
        margin-left: auto;
        box-sizing: border-box;
    }

    .totals-row {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid #000;
        padding: 7px 0;
        font-size: 14px;
        color: #000;
    }

    .totals-row:last-child {
        border-bottom: 0;
        font-weight: 700;
    }

    .signature-area {
        margin-top: 24px;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .signature-box {
        text-align: center;
    }

    .signature-line {
        border-top: 1px solid #000;
        margin-top: 50px;
        padding-top: 9px;
        font-size: 14px;
        font-weight: 600;
        color: #000;
    }

    .payroll-edit-input {
        width: 72px;
        max-width: 100%;
        padding: 4px 6px;
        font-size: 13px;
        text-align: center;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
    }

    .payroll-notes-input {
        width: 100%;
        min-width: 90px;
        padding: 4px 6px;
        font-size: 12px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
    }

    .payroll-edit-hint {
        display: block;
        font-size: 11px;
        color: #64748b;
        margin-top: 2px;
    }

    .print-only {
        display: none;
    }

    @media print {
        .payroll-edit-input,
        .payroll-notes-input,
        .no-print {
            display: none !important;
        }
        .print-only {
            display: inline !important;
        }
    }

</style>

@php
    $status = $payrollRegister->status;
    $month = $month ?? $currentMonth;
    $year = $year ?? $currentYear;

    $totalBase = 0;
    $totalHousing = 0;
    $totalTransport = 0;
    $totalOtherAllowances = 0;
    $totalOvertime = 0;
    $totalLeaveDeduction = 0;
    $totalOtherDeduction = 0;
    $totalAdvanceDeduction = 0;
    $totalPayroll = 0;
@endphp

<div class="payroll-page-shell">
    @if(session('success'))
        <div class="alert-success no-print" style="margin-bottom: 12px;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert-danger no-print" style="margin-bottom: 12px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="payroll-actions no-print">
        <a href="{{ route('employees.payroll-registers.index') }}" class="btn btn-secondary">
            {{ __('employees.payroll_registers_link') }}
        </a>

        @if($payrollRegister->status === 'approved')
            <span class="badge badge-green" style="padding:8px 12px;">{{ __('employees.payroll_status_approved') }}</span>
            @if(!($hasPendingRegister ?? false))
                <form method="POST" action="{{ route('employees.payroll-registers.create') }}">
                    @csrf
                    <button type="submit" class="btn btn-success">{{ __('employees.payroll_new_btn') }}</button>
                </form>
            @endif
        @else
            <button type="submit" form="payroll-edit-form" class="btn btn-warning">
                {{ __('employees.payroll_save_btn') }}
            </button>
            <form method="POST" action="{{ route('employees.payroll-register.approve') }}" style="display:inline;">
                @csrf
                <input type="hidden" name="payroll_register_id" value="{{ $payrollRegister->id }}">
                <button type="submit" class="btn btn-success" onclick="return confirm(@json(__('employees.payroll_confirm_approve')))">
                    {{ __('employees.payroll_approve_btn') }}
                </button>
            </form>
        @endif

        <button type="button" class="btn btn-primary" onclick="window.print()">
            {{ __('employees.payroll_print_btn') }}
        </button>
    </div>

    @if($canEditPayroll ?? false)
    <form method="POST" action="{{ route('employees.payroll-register.update-adjustments', $payrollRegister) }}" id="payroll-edit-form">
        @csrf
    @endif

    <div class="print-area">
        <div class="payroll-paper">
            <div class="paper-header">
                <div class="paper-header-logo">
                    <x-company-print-logo />
                </div>
                <div class="paper-header-center">
                    <h1>شركة التقدم للخرسانة الجاهزة</h1>
                    <h2>{{ __('employees.payroll_sheet_title') }}</h2>
                    <p>{{ __('employees.payroll_period_label', ['period' => $month.'/'.$year]) }}</p>
                </div>

                <div class="paper-header-right">
                    <span>{{ __('employees.payroll_status_label') }}: {{ $status == 'approved' ? __('employees.payroll_status_approved') : __('employees.payroll_status_pending') }}</span>
                    @if($payrollRegister->approved_at)
                        <div style="font-size:12px; font-weight:400; margin-top:4px;">
                            {{ __('employees.payroll_th_approved_at') }}: {{ $payrollRegister->approved_at->format('Y-m-d') }}
                        </div>
                    @endif
                </div>
            </div>

            @if($canEditPayroll ?? false)
            <p class="no-print panel-subtitle" style="margin-bottom:12px;">{{ __('employees.payroll_edit_hint') }}</p>
            @endif

            <table>
                <thead>
                    <tr>
                        <th>الموظف</th>
                        <th>الراتب الأساسي</th>
                        <th>بدل سكن</th>
                        <th>بدل مواصلات</th>
                        <th>بدلات أخرى</th>
                        <th>الإضافي</th>
                        <th>خصم الإجازات</th>
                        <th>خصم آخر</th>
                        <th>خصم السلفة</th>
                        <th>الإجمالي</th>
                        <th>ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        @php
                            $adjustment = $adjustments[$employee->id] ?? null;
                            $payrollRow = app(\App\Services\PayrollCalculationService::class)
                                ->calculate($employee, $adjustment, (int) $currentMonth, (int) $currentYear, (int) $payrollRegister->id);
                            $base = $payrollRow['base_salary'];
                            $housing = $payrollRow['housing'];
                            $transport = $payrollRow['transport'];
                            $otherAllowances = $payrollRow['other_allowances'];
                            $overtime = $payrollRow['overtime_amount'];
                            $leaveDeduction = $payrollRow['leave_deduction'];
                            $otherDeduction = $payrollRow['other_deduction'];
                            $advanceDeduction = $payrollRow['advance_deduction'];
                            $rowTotal = $payrollRow['final_salary'];

                            $totalBase += $base;
                            $totalHousing += $housing;
                            $totalTransport += $transport;
                            $totalOtherAllowances += $otherAllowances;
                            $totalOvertime += $overtime;
                            $totalLeaveDeduction += $leaveDeduction;
                            $totalOtherDeduction += $otherDeduction;
                            $totalAdvanceDeduction = ($totalAdvanceDeduction ?? 0) + $advanceDeduction;
                            $totalPayroll += $rowTotal;
                        @endphp
                        <tr>
                            <td class="name-col">
                                <a href="{{ route('employees.show', $employee) }}" class="no-print" style="color:inherit;">{{ $employee->name }}</a>
                                <span class="print-only">{{ $employee->name }}</span>
                            </td>
                            <td>{{ number_format($base, 2) }}</td>
                            <td>{{ number_format($housing, 2) }}</td>
                            <td>{{ number_format($transport, 2) }}</td>
                            <td>{{ number_format($otherAllowances, 2) }}</td>
                            <td>
                                @if($canEditPayroll ?? false)
                                    <input type="number" step="0.01" min="0" class="payroll-edit-input no-print"
                                        name="adjustments[{{ $employee->id }}][overtime_hours]"
                                        value="{{ old('adjustments.'.$employee->id.'.overtime_hours', $adjustment->overtime_hours ?? 0) }}">
                                    <span class="payroll-edit-hint no-print">{{ number_format($overtime, 2) }}</span>
                                    <span class="print-only">{{ number_format($overtime, 2) }}</span>
                                @else
                                    {{ number_format($overtime, 2) }}
                                @endif
                            </td>
                            <td>
                                @if($canEditPayroll ?? false)
                                    <input type="number" step="0.01" min="0" class="payroll-edit-input no-print"
                                        name="adjustments[{{ $employee->id }}][leave_deduction_days]"
                                        value="{{ old('adjustments.'.$employee->id.'.leave_deduction_days', $adjustment->leave_deduction_days ?? 0) }}">
                                    <span class="payroll-edit-hint no-print">{{ number_format($leaveDeduction, 2) }}</span>
                                    <span class="print-only">{{ number_format($leaveDeduction, 2) }}</span>
                                @else
                                    {{ number_format($leaveDeduction, 2) }}
                                @endif
                            </td>
                            <td>
                                @if($canEditPayroll ?? false)
                                    <input type="number" step="0.01" min="0" class="payroll-edit-input no-print"
                                        name="adjustments[{{ $employee->id }}][other_deduction]"
                                        value="{{ old('adjustments.'.$employee->id.'.other_deduction', $adjustment->other_deduction ?? 0) }}">
                                    <span class="print-only">{{ number_format($otherDeduction, 2) }}</span>
                                @else
                                    {{ number_format($otherDeduction, 2) }}
                                @endif
                            </td>
                            <td>{{ number_format($advanceDeduction, 2) }}</td>
                            <td>{{ number_format($rowTotal, 2) }}</td>
                            <td>
                                @if($canEditPayroll ?? false)
                                    <input type="text" class="payroll-notes-input no-print"
                                        name="adjustments[{{ $employee->id }}][notes]"
                                        value="{{ old('adjustments.'.$employee->id.'.notes', $adjustment->notes ?? '') }}">
                                    <span class="print-only">{{ $adjustment->notes ?? '' }}</span>
                                @else
                                    {{ $adjustment->notes ?? '' }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">لا يوجد موظفون لعرضهم</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @php
                $totalAllowances = $totalHousing + $totalTransport + $totalOtherAllowances;
                $totalDeductions = $totalLeaveDeduction + $totalOtherDeduction + $totalAdvanceDeduction;
            @endphp

            <div class="summary-box">
                <div class="totals-row">
                    <span>إجمالي الرواتب</span>
                    <span>{{ number_format($totalBase, 2) }}</span>
                </div>
                <div class="totals-row">
                    <span>إجمالي البدلات</span>
                    <span>{{ number_format($totalAllowances, 2) }}</span>
                </div>
                <div class="totals-row">
                    <span>إجمالي الإضافي</span>
                    <span>{{ number_format($totalOvertime, 2) }}</span>
                </div>
                <div class="totals-row">
                    <span>إجمالي الخصومات</span>
                    <span>{{ number_format($totalDeductions, 2) }}</span>
                </div>
                <div class="totals-row">
                    <span>الإجمالي النهائي للمسير</span>
                    <span>{{ number_format($totalPayroll, 2) }}</span>
                </div>
            </div>

            @if(($advancePayments ?? collect())->isNotEmpty())
                <div style="margin-top:20px;">
                    <h3 style="margin:0 0 10px; font-size:16px;">{{ __('employees.payroll_advance_payments_title') }}</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __('employees.payroll_advance_th_employee') }}</th>
                                <th>{{ __('employees.payroll_advance_th_amount') }}</th>
                                <th>{{ __('employees.payroll_advance_th_installment') }}</th>
                                <th>{{ __('employees.payroll_advance_th_recorded_at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($advancePayments as $payment)
                                <tr>
                                    <td class="name-col">{{ $payment->advance?->employee?->name ?? '-' }}</td>
                                    <td>{{ number_format((float) $payment->amount, 2) }}</td>
                                    <td>
                                        {{ (int) ($payment->advance?->installments_paid ?? 0) }}
                                        /
                                        {{ (int) ($payment->advance?->installment_count ?? 0) }}
                                    </td>
                                    <td>{{ $payment->recorded_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div style="margin-top:40px; border-top:2px solid #000; padding-top:20px;">
                <table style="width:100%; border-collapse:collapse; text-align:center;">
                    <tr>
                        <td style="border:1px solid #000; padding:30px;">
                            إعداد الموارد البشرية
                            <br><br><br>
                            ________
                        </td>

                        <td style="border:1px solid #000; border-left:2px solid #000; border-right:2px solid #000; padding:30px;">
                            اعتماد الإدارة
                            <br><br><br>
                            ________
                        </td>

                        <td style="border:1px solid #000; padding:30px;">
                            ختم الشركة
                            <br><br><br>
                            ________
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    @if($canEditPayroll ?? false)
    </form>
    @endif
</div>

<style>
@media print {
    @page {
        size: A4 landscape;
        margin: 0;
    }

    html, body {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
    }

    .company-print-logo {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    body * {
        visibility: hidden !important;
    }

    .print-area,
    .print-area * {
        visibility: visible !important;
    }

    .print-area {
        position: fixed !important;
        inset: 0 !important;
        width: 100vw !important;
        min-height: 100vh !important;
        margin: 0 !important;
        padding: 12mm !important;
        background: #fff !important;
        color: #000 !important;
        box-shadow: none !important;
    }

    .no-print,
    .topbar,
    .sidebar,
    nav,
    header,
    footer {
        display: none !important;
        visibility: hidden !important;
    }
}
</style>
@endsection
