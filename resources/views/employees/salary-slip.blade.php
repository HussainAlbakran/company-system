@extends('layouts.app')

@section('page_title', __('employees.salary_slip_title'))
@section('page_subtitle', __('employees.salary_slip_subtitle'))

@section('content')
<style>
    .slip-actions {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .slip-paper {
        background: #fff;
        color: #000;
        border: 1px solid #000;
        padding: 16px;
        margin-bottom: 24px;
    }

    .slip-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        border-bottom: 1px solid #000;
        padding-bottom: 10px;
        margin-bottom: 12px;
    }

    .slip-header-logo {
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
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .slip-header-center {
        text-align: center;
        flex: 1;
    }

    .slip-header-center h1,
    .slip-header-center h2,
    .slip-header-center p {
        margin: 0;
        line-height: 1.6;
        color: #000;
    }

    .slip-meta {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 12px;
        font-size: 13px;
        color: #000;
        font-weight: 600;
    }

    .slip-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .slip-table th,
    .slip-table td {
        border: 1px solid #000;
        padding: 6px 8px;
        color: #000;
        text-align: center;
        vertical-align: middle;
    }

    .slip-table th {
        background: #f3f4f6;
        font-weight: 700;
    }

    .slip-table td.name-cell {
        text-align: start;
        font-weight: 600;
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
        .no-print {
            display: none !important;
        }

        .slip-paper {
            border: none;
        }
    }
</style>

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

    <div class="slip-paper">
        <div class="slip-header">
            <div class="slip-header-logo">
                <x-company-print-logo />
            </div>
            <div class="slip-header-center">
                <h1>{{ __('employees.salary_slip_sheet_title') }}</h1>
                <h2>{{ __('employees.salary_slip_year_heading', ['years' => $yearLabel]) }}</h2>
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
            <div style="min-width:130px; text-align:end; color:#000; font-weight:700; line-height:1.7;">
                <div>{{ __('employees.salary_slip_date_year') }}: {{ $printDate->format('Y') }}</div>
                <div>{{ __('employees.salary_slip_date_month') }}: {{ $printDate->format('m') }}</div>
                <div>{{ __('employees.salary_slip_date_day') }}: {{ $printDate->format('d') }}</div>
                <div>{{ $printDate->format('Y-m-d') }}</div>
            </div>
        </div>

        <div class="slip-meta">
            @if($mode === 'last_12' && $fromDate && $toDate)
                <div>{{ __('employees.salary_slip_range', ['from' => $fromDate->format('Y-m-d'), 'to' => $toDate->format('Y-m-d')]) }}</div>
            @endif
            <div>{{ __('employees.salary_slip_rows_count', ['count' => count($rows)]) }}</div>
        </div>

        <div style="overflow-x:auto;">
            <table class="slip-table">
                <thead>
                    <tr>
                        <th>{{ __('employees.payroll_th_number') }}</th>
                        <th>{{ __('employees.th_name') }}</th>
                        <th>{{ __('employees.salary') }}</th>
                        <th>{{ __('employees.salary_slip_th_housing') }}</th>
                        <th>{{ __('employees.salary_slip_th_transport') }}</th>
                        <th>{{ __('employees.salary_slip_th_other_allowances') }}</th>
                        <th>{{ __('employees.salary_slip_th_deductions') }}</th>
                        <th>{{ __('employees.salary_slip_th_advance') }}</th>
                        <th>{{ __('employees.salary_slip_th_period_date') }}</th>
                        <th>{{ __('employees.salary_slip_th_total') }}</th>
                        <th>{{ __('employees.salary_slip_th_paid_at') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grand = 0; @endphp
                    @forelse($rows as $row)
                        @php $grand += (float) $row['total']; @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="name-cell">{{ $row['employee']->name }}</td>
                            <td>{{ number_format($row['base_salary'], 2) }}</td>
                            <td>{{ number_format($row['housing'], 2) }}</td>
                            <td>{{ number_format($row['transport'], 2) }}</td>
                            <td>{{ number_format($row['other_allowances'], 2) }}</td>
                            <td>{{ number_format($row['deductions'], 2) }}</td>
                            <td>{{ number_format($row['advance'], 2) }}</td>
                            <td>{{ $row['period_date'] }}</td>
                            <td style="font-weight:700;">{{ number_format($row['total'], 2) }}</td>
                            <td>{{ $row['paid_at'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11">{{ __('employees.salary_slip_empty') }}</td>
                        </tr>
                    @endforelse
                    @if(count($rows) > 0)
                        <tr>
                            <td colspan="9" style="text-align:end; font-weight:700;">{{ __('employees.salary_slip_th_total') }}</td>
                            <td style="font-weight:700;">{{ number_format($grand, 2) }}</td>
                            <td></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
