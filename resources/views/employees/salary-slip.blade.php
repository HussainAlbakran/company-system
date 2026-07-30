@extends('layouts.app')

@section('page_title', __('employees.salary_slip_title'))
@section('page_subtitle', __('employees.salary_slip_subtitle'))

@section('content')
<style>
    .slip-page-shell {
        padding: 0;
    }

    .slip-actions {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .slip-print-area {
        width: 100%;
        max-width: 900px;
        margin: 0 auto;
        overflow-x: auto;
    }

    .slip-paper {
        background: #fff;
        color: #000;
        border: 1px solid #000;
        width: 100%;
        max-width: 900px;
        margin: 0 auto;
        padding: 14px;
        box-sizing: border-box;
        overflow: visible;
    }

    .slip-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        border-bottom: 2px solid #000;
        padding-bottom: 8px;
        margin-bottom: 10px;
    }

    .slip-header-logo {
        width: 90px;
        min-width: 90px;
        flex-shrink: 0;
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
    }

    .company-print-logo {
        width: 82px;
        height: 82px;
        max-width: 100%;
        object-fit: contain;
        display: block;
    }

    .slip-header-center {
        text-align: center;
        flex: 1;
    }

    .slip-header-center h1,
    .slip-header-center h2,
    .slip-header-center p {
        margin: 0;
        line-height: 1.45;
        color: #000;
    }

    .slip-header-center h1 {
        font-size: 20px;
        font-weight: 700;
    }

    .slip-header-center h2 {
        font-size: 17px;
        font-weight: 700;
        margin-top: 2px;
    }

    .slip-header-center p {
        font-size: 12px;
        margin-top: 2px;
    }

    .slip-header-right {
        min-width: 150px;
        border: 1px solid #000;
        padding: 8px 10px;
        box-sizing: border-box;
        text-align: start;
        color: #000;
        font-weight: 700;
        line-height: 1.65;
        font-size: 12px;
        background: #fff;
    }

    .slip-meta {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 10px;
        font-size: 12px;
        color: #000;
        font-weight: 600;
    }

    .slip-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .slip-page-chunk {
        width: 100%;
    }

    .slip-page-chunk + .slip-page-chunk {
        margin-top: 16px;
    }

    .slip-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
        margin-bottom: 0;
        table-layout: fixed;
    }

    .slip-table th,
    .slip-table td {
        border: 1px solid #000;
        padding: 5px 3px;
        color: #000;
        text-align: center;
        vertical-align: middle;
        background: #fff;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .slip-table th {
        font-weight: 700;
        background: #f5f5f5;
        font-size: 10px;
        line-height: 1.3;
    }

    .slip-table td.name-cell {
        text-align: start;
        font-weight: 600;
        white-space: normal;
        width: 16%;
    }

    .slip-table th.num-col,
    .slip-table td.num-col {
        width: 4%;
        font-weight: 700;
    }

    .slip-table td.total-cell {
        font-weight: 700;
    }

    .slip-summary {
        border: 1px solid #000;
        padding: 8px 10px;
        width: 280px;
        background: #fff;
        margin: 14px 0 0 auto;
        box-sizing: border-box;
    }

    .slip-summary-row {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid #000;
        padding: 6px 0;
        font-size: 12px;
        color: #000;
    }

    .slip-summary-row:last-child {
        border-bottom: 0;
        font-weight: 700;
    }

    .slip-signatures-wrap {
        margin-top: 28px;
        border-top: 2px solid #000;
        padding-top: 14px;
    }

    .slip-signatures {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
    }

    .slip-signatures td {
        border: 1px solid #000;
        padding: 26px 10px;
        color: #000;
        font-weight: 600;
        width: 33.33%;
        vertical-align: top;
        font-size: 13px;
        background: #fff;
    }

    .mode-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 14px;
        margin-top: 16px;
    }

    .mode-card {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 18px;
        background: #fff;
    }

    .mode-card h3 {
        margin: 0 0 8px;
        color: #000;
        font-size: 16px;
    }

    .mode-card p {
        margin: 0 0 14px;
        color: #4b5563;
        font-size: 13px;
    }

    .employee-filter {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 14px;
        background: #fff;
        margin-bottom: 16px;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            height: auto !important;
            overflow: visible !important;
            color: #000 !important;
        }

        .no-print,
        .topbar,
        .sidebar,
        .sidebar-backdrop,
        nav,
        header,
        footer,
        .slip-actions,
        .employee-filter,
        .brand-box {
            display: none !important;
        }

        .main-layout,
        .layout-content,
        .page-content,
        .content-area,
        .dashboard-stack,
        .slip-page-shell,
        main {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            overflow: visible !important;
            height: auto !important;
            min-height: 0 !important;
            box-shadow: none !important;
        }

        .slip-print-area,
        .slip-paper {
            position: static !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            border: 0 !important;
            background: #fff !important;
            color: #000 !important;
            box-shadow: none !important;
            overflow: visible !important;
        }

        .slip-header {
            gap: 8px !important;
        }

        .slip-header-logo {
            width: 64px !important;
            min-width: 64px !important;
        }

        .slip-header-center h1 {
            font-size: 15px !important;
        }

        .slip-header-center h2 {
            font-size: 13px !important;
        }

        .slip-header-center p {
            font-size: 10px !important;
        }

        .slip-header-right {
            min-width: 110px !important;
            padding: 6px 7px !important;
            font-size: 10px !important;
        }

        .slip-table-wrap {
            overflow: visible !important;
        }

        .slip-page-chunk + .slip-page-chunk {
            margin-top: 0 !important;
        }

        .slip-page-break {
            break-after: page;
            page-break-after: always;
        }

        .slip-table {
            font-size: 8px !important;
            width: 100% !important;
            max-width: 100% !important;
            table-layout: fixed !important;
        }

        .slip-table th,
        .slip-table td {
            padding: 3px 1px !important;
            font-size: 8px !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .slip-table th {
            background: #f5f5f5 !important;
            font-size: 7.5px !important;
            line-height: 1.2 !important;
        }

        .slip-table td.name-cell {
            white-space: normal !important;
            width: 14% !important;
            font-size: 7.5px !important;
        }

        .slip-table thead {
            display: table-header-group;
        }

        .slip-table tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .slip-header,
        .slip-meta,
        .slip-summary,
        .slip-signatures-wrap {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .slip-summary {
            width: 220px !important;
            font-size: 10px !important;
        }

        .slip-signatures td {
            padding: 16px 6px !important;
            font-size: 11px !important;
        }

        .company-print-logo {
            width: 58px !important;
            height: 58px !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        a {
            text-decoration: none !important;
            color: #000 !important;
        }
    }
</style>

<div class="slip-page-shell">
@if(session('success'))
    <div class="alert-success no-print" style="margin-bottom:12px;">{{ session('success') }}</div>
@endif

<div class="slip-actions no-print">
    <a href="{{ route('employees.index') }}" class="btn btn-secondary">{{ __('employees.salary_slip_back') }}</a>

    @if(in_array($mode, ['year', 'last_12'], true))
        <button type="button" class="btn btn-primary" onclick="window.print()">
            {{ __('employees.salary_slip_print') }}
        </button>
        <a href="{{ route('employees.salary-slip') }}" class="btn btn-secondary">
            {{ __('employees.salary_slip_change_mode') }}
        </a>
    @endif
</div>

@if(! in_array($mode, ['year', 'last_12'], true))
    <section class="dashboard-panel no-print">
        <h2 class="panel-title">{{ __('employees.salary_slip_choose_title') }}</h2>
        <p class="panel-subtitle">{{ __('employees.salary_slip_choose_subtitle') }}</p>

        <div class="mode-cards">
            <div class="mode-card">
                <h3>{{ __('employees.salary_slip_mode_year') }}</h3>
                <p>{{ __('employees.salary_slip_mode_year_hint') }}</p>
                <form method="GET" action="{{ route('employees.salary-slip') }}">
                    <input type="hidden" name="mode" value="year">
                    <div class="form-group" style="margin-bottom:12px;">
                        <label>{{ __('employees.salary_slip_year') }}</label>
                        <select name="year" required>
                            @foreach($availableYears as $y)
                                <option value="{{ $y }}" @selected((int) $selectedYear === (int) $y)>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:12px;">
                        <label>{{ __('employees.salary_slip_employee') }}</label>
                        <input
                            type="text"
                            name="employee_query"
                            list="salary-slip-employees"
                            placeholder="{{ __('employees.salary_slip_employee_placeholder') }}"
                            autocomplete="off"
                        >
                    </div>
                    <div class="form-group" style="margin-bottom:12px;">
                        <label>{{ __('employees.salary_slip_employee_select') }}</label>
                        <select name="employee_id">
                            <option value="">{{ __('employees.salary_slip_employee_all') }}</option>
                            @foreach($employeeList as $employee)
                                <option value="{{ $employee->id }}">
                                    {{ $employee->name }}{{ $employee->employee_number ? ' — '.$employee->employee_number : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('employees.salary_slip_show') }}</button>
                </form>
            </div>

            <div class="mode-card">
                <h3>{{ __('employees.salary_slip_mode_last12') }}</h3>
                <p>{{ __('employees.salary_slip_mode_last12_hint') }}</p>
                <form method="GET" action="{{ route('employees.salary-slip') }}">
                    <input type="hidden" name="mode" value="last_12">
                    <div class="form-group" style="margin-bottom:12px;">
                        <label>{{ __('employees.salary_slip_employee') }}</label>
                        <input
                            type="text"
                            name="employee_query"
                            list="salary-slip-employees"
                            placeholder="{{ __('employees.salary_slip_employee_placeholder') }}"
                            autocomplete="off"
                        >
                    </div>
                    <div class="form-group" style="margin-bottom:12px;">
                        <label>{{ __('employees.salary_slip_employee_select') }}</label>
                        <select name="employee_id">
                            <option value="">{{ __('employees.salary_slip_employee_all') }}</option>
                            @foreach($employeeList as $employee)
                                <option value="{{ $employee->id }}">
                                    {{ $employee->name }}{{ $employee->employee_number ? ' — '.$employee->employee_number : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">{{ __('employees.salary_slip_show') }}</button>
                </form>
            </div>
        </div>

        <datalist id="salary-slip-employees">
            @foreach($employeeList as $employee)
                <option value="{{ $employee->name }}"></option>
            @endforeach
        </datalist>
    </section>
@else
    <div class="employee-filter no-print">
        <form method="GET" action="{{ route('employees.salary-slip') }}" class="form-grid" style="align-items:end;">
            <input type="hidden" name="mode" value="{{ $mode }}">
            @if($mode === 'year')
                <input type="hidden" name="year" value="{{ $selectedYear }}">
            @endif
            <div class="form-group">
                <label>{{ __('employees.salary_slip_employee') }}</label>
                <input
                    type="text"
                    name="employee_query"
                    list="salary-slip-employees-filter"
                    value="{{ $employeeQuery }}"
                    placeholder="{{ __('employees.salary_slip_employee_placeholder') }}"
                    autocomplete="off"
                >
            </div>
            <div class="form-group">
                <label>{{ __('employees.salary_slip_employee_select') }}</label>
                <select name="employee_id">
                    <option value="">{{ __('employees.salary_slip_employee_all') }}</option>
                    @foreach($employeeList as $employee)
                        <option value="{{ $employee->id }}" @selected((int) $selectedEmployeeId === (int) $employee->id)>
                            {{ $employee->name }}{{ $employee->employee_number ? ' — '.$employee->employee_number : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">{{ __('employees.salary_slip_filter') }}</button>
                <button type="button" class="btn btn-success" onclick="window.print()">{{ __('employees.salary_slip_print') }}</button>
            </div>
        </form>
        <datalist id="salary-slip-employees-filter">
            @foreach($employeeList as $employee)
                <option value="{{ $employee->name }}"></option>
            @endforeach
        </datalist>
    </div>

    @php
        $rowsCollection = collect($rows);
        $rowsPerPrintPage = 16;
        $rowChunks = $rowsCollection->values()->chunk($rowsPerPrintPage);

        $sumBase = $rowsCollection->sum('base_salary');
        $sumHousing = $rowsCollection->sum('housing');
        $sumTransport = $rowsCollection->sum('transport');
        $sumOther = $rowsCollection->sum('other_allowances');
        $sumDeductions = $rowsCollection->sum('deductions');
        $sumAdvance = $rowsCollection->sum('advance');
        $sumTotal = $rowsCollection->sum('total');
        $sumAllowances = $sumHousing + $sumTransport + $sumOther;
    @endphp

    <div class="slip-print-area">
        <div class="slip-paper">
            <div class="slip-header">
                <div class="slip-header-logo">
                    <x-company-print-logo />
                </div>
                <div class="slip-header-center">
                    <h1>{{ __('employees.company_legal_name') }}</h1>
                    <h2>{{ __('employees.salary_slip_sheet_title') }}</h2>
                    <p>{{ __('employees.salary_slip_year_heading', ['years' => $yearLabel]) }}</p>
                    <p>
                        {{ __('employees.salary_slip_mode_label', [
                            'mode' => $mode === 'last_12'
                                ? __('employees.salary_slip_mode_last12')
                                : __('employees.salary_slip_mode_year'),
                        ]) }}
                    </p>
                    @if($selectedEmployee)
                        <p>{{ __('employees.salary_slip_employee_heading', ['name' => $selectedEmployee->name]) }}</p>
                    @endif
                </div>
                <div class="slip-header-right">
                    <div>{{ __('employees.salary_slip_date_year') }}: {{ $printDate->format('Y') }}</div>
                    <div>{{ __('employees.salary_slip_date_month') }}: {{ $printDate->format('m') }}</div>
                    <div>{{ __('employees.salary_slip_date_day') }}: {{ $printDate->format('d') }}</div>
                    <div>{{ $printDate->format('Y-m-d') }}</div>
                </div>
            </div>

            <div class="slip-meta">
                @if($mode === 'last_12' && $fromDate && $toDate)
                    <div>{{ __('employees.salary_slip_range', ['from' => $fromDate->format('Y-m-d'), 'to' => $toDate->format('Y-m-d')]) }}</div>
                @else
                    <div>{{ __('employees.salary_slip_year_heading', ['years' => $yearLabel]) }}</div>
                @endif
                <div>{{ __('employees.salary_slip_rows_count', ['count' => $rowsCollection->count()]) }}</div>
            </div>

            <div class="slip-table-wrap">
                @forelse($rowChunks as $chunkIndex => $chunk)
                    <div class="slip-page-chunk {{ $loop->last ? '' : 'slip-page-break' }}">
                        <table class="slip-table">
                            <thead>
                                <tr>
                                    <th class="num-col">{{ __('employees.payroll_th_number') }}</th>
                                    <th>{{ __('employees.th_name') }}</th>
                                    <th>{{ __('employees.salary') }}</th>
                                    <th>{{ __('employees.salary_slip_th_housing') }}</th>
                                    <th>{{ __('employees.salary_slip_th_transport') }}</th>
                                    <th>{{ __('employees.salary_slip_th_other_allowances') }}</th>
                                    <th>{{ __('employees.salary_slip_th_deductions') }}</th>
                                    <th>{{ __('employees.salary_slip_th_advance') }}</th>
                                    <th>{{ __('employees.salary_slip_th_total') }}</th>
                                    <th>{{ __('employees.salary_slip_th_paid_at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($chunk as $row)
                                    @php
                                        $rowNumber = ($chunkIndex * $rowsPerPrintPage) + $loop->iteration;
                                    @endphp
                                    <tr>
                                        <td class="num-col">{{ $rowNumber }}</td>
                                        <td class="name-cell">{{ $row['employee']->name }}</td>
                                        <td>{{ number_format($row['base_salary'], 2) }}</td>
                                        <td>{{ number_format($row['housing'], 2) }}</td>
                                        <td>{{ number_format($row['transport'], 2) }}</td>
                                        <td>{{ number_format($row['other_allowances'], 2) }}</td>
                                        <td>{{ number_format($row['deductions'], 2) }}</td>
                                        <td>{{ number_format($row['advance'], 2) }}</td>
                                        <td class="total-cell">{{ number_format($row['total'], 2) }}</td>
                                        <td>{{ $row['paid_at'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @empty
                    <table class="slip-table">
                        <thead>
                            <tr>
                                <th class="num-col">{{ __('employees.payroll_th_number') }}</th>
                                <th>{{ __('employees.th_name') }}</th>
                                <th>{{ __('employees.salary') }}</th>
                                <th>{{ __('employees.salary_slip_th_housing') }}</th>
                                <th>{{ __('employees.salary_slip_th_transport') }}</th>
                                <th>{{ __('employees.salary_slip_th_other_allowances') }}</th>
                                <th>{{ __('employees.salary_slip_th_deductions') }}</th>
                                <th>{{ __('employees.salary_slip_th_advance') }}</th>
                                <th>{{ __('employees.salary_slip_th_total') }}</th>
                                <th>{{ __('employees.salary_slip_th_paid_at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="10">{{ __('employees.salary_slip_empty') }}</td>
                            </tr>
                        </tbody>
                    </table>
                @endforelse
            </div>

            @if($rowsCollection->isNotEmpty())
                <div class="slip-summary">
                    <div class="slip-summary-row">
                        <span>{{ __('employees.payroll_summary_total_salaries') }}</span>
                        <span>{{ number_format($sumBase, 2) }}</span>
                    </div>
                    <div class="slip-summary-row">
                        <span>{{ __('employees.payroll_summary_total_allowances') }}</span>
                        <span>{{ number_format($sumAllowances, 2) }}</span>
                    </div>
                    <div class="slip-summary-row">
                        <span>{{ __('employees.payroll_summary_total_deductions') }}</span>
                        <span>{{ number_format($sumDeductions + $sumAdvance, 2) }}</span>
                    </div>
                    <div class="slip-summary-row">
                        <span>{{ __('employees.salary_slip_th_total') }}</span>
                        <span>{{ number_format($sumTotal, 2) }}</span>
                    </div>
                </div>
            @endif

            <div class="slip-signatures-wrap">
                <table class="slip-signatures">
                    <tr>
                        <td>
                            {{ __('employees.salary_slip_sign_hr') }}
                            <br><br><br>
                            ________
                        </td>
                        <td>
                            {{ __('employees.salary_slip_sign_mgmt') }}
                            <br><br><br>
                            ________
                        </td>
                        <td>
                            {{ __('employees.salary_slip_sign_stamp') }}
                            <br><br><br>
                            ________
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endif
</div>
@endsection
