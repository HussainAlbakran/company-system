@extends('layouts.app')

@section('page_title', 'مسير الرواتب')
@section('page_subtitle', 'صفحة مسير الرواتب')

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
    $totalPayroll = 0;
@endphp

<div class="payroll-page-shell">
    @if(session('success'))
        <div class="alert-success no-print" style="margin-bottom: 12px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="payroll-actions no-print">
        @if($payrollRegister->status === 'approved')
            <button type="button" class="btn btn-secondary" disabled>
                تم اعتماد كشف الرواتب
            </button>
        @else
            <form method="POST" action="{{ route('employees.payroll-register.approve') }}">
                @csrf
                <button type="submit" class="btn btn-success">
                    اعتماد كشف الرواتب
                </button>
            </form>
        @endif

        <button type="button" class="btn btn-primary" onclick="window.print()">
            طباعة كشف الرواتب
        </button>
    </div>

    <div class="print-area">
        <div class="payroll-paper">
            <div style="position:absolute; top:20px; left:20px;">
            </div>
            <div class="paper-header">
                <div class="paper-header-center">
                    <h1>شركة التقدم للخرسانة الجاهزة</h1>
                    <h2>مسير الرواتب</h2>
                    <p>الشهر: {{ $month }}/{{ $year }}</p>
                </div>

                <div class="paper-header-right">
                    <span>الحالة: {{ $status == 'approved' ? 'معتمد' : 'غير معتمد' }}</span>
                </div>
            </div>

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
                        <th>الإجمالي</th>
                        <th>ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        @php
                            $adjustment = $adjustments[$employee->id] ?? null;
                            $base = (float) ($employee->salary ?? 0);
                            $housing = (float) ($employee->housing_allowance ?? 0);
                            $transport = (float) ($employee->transportation_allowance ?? 0);
                            $travel = (float) ($employee->travel_allowance ?? 0);
                            $risk = (float) ($employee->risk_allowance ?? 0);
                            $transfer = (float) ($employee->transfer_allowance ?? 0);
                            $otherAllowances = $travel + $risk + $transfer;

                            $overtimeHours = (float) ($adjustment->overtime_hours ?? 0);
                            $leaveDeductionDays = (float) ($adjustment->leave_deduction_days ?? 0);
                            $hourlyRate = $base / 240;
                            $overtimeHourRate = $hourlyRate * 1.5;
                            $overtime = $overtimeHours * $overtimeHourRate;
                            $dailyRate = $base / 30;
                            $leaveDeduction = $leaveDeductionDays * $dailyRate;
                            $otherDeduction = (float) ($adjustment->other_deduction ?? 0);
                            $gross = $base + $housing + $transport + $otherAllowances + $overtime;
                            $rowTotal = $gross - $leaveDeduction - $otherDeduction;

                            $totalBase += $base;
                            $totalHousing += $housing;
                            $totalTransport += $transport;
                            $totalOtherAllowances += $otherAllowances;
                            $totalOvertime += $overtime;
                            $totalLeaveDeduction += $leaveDeduction;
                            $totalOtherDeduction += $otherDeduction;
                            $totalPayroll += $rowTotal;
                        @endphp
                        <tr>
                            <td class="name-col">{{ $employee->name }}</td>
                            <td>{{ number_format($base, 2) }}</td>
                            <td>{{ number_format($housing, 2) }}</td>
                            <td>{{ number_format($transport, 2) }}</td>
                            <td>{{ number_format($otherAllowances, 2) }}</td>
                            <td>{{ number_format($overtime, 2) }}</td>
                            <td>{{ number_format($leaveDeduction, 2) }}</td>
                            <td>{{ number_format($otherDeduction, 2) }}</td>
                            <td>{{ number_format($rowTotal, 2) }}</td>
                            <td>{{ $adjustment->notes ?? '' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">لا يوجد موظفون لعرضهم</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @php
                $totalAllowances = $totalHousing + $totalTransport + $totalOtherAllowances;
                $totalDeductions = $totalLeaveDeduction + $totalOtherDeduction;
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
