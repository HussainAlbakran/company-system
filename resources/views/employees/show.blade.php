@extends('layouts.app')

@section('page_title', __('employees.show_title'))
@section('page_subtitle', __('employees.show_subtitle'))

@section('content')
<style>
    .employee-profile-page .summary-grid {
        display: grid;
        gap: 10px;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }

    .employee-profile-page .summary-box {
        min-width: 0;
    }

    .employee-profile-page .summary-label {
        display: block;
        margin-bottom: 6px;
        color: #111827;
        font-size: 11px;
        font-weight: 700;
    }

    .employee-profile-page .summary-value {
        min-width: 0;
        overflow-wrap: anywhere;
        word-break: break-word;
        color: #111827;
        line-height: 1.45;
    }

    .employee-profile-page .page-header h2 {
        color: #111827;
    }

    .employee-profile-page .asset-form-grid {
        display: grid;
        gap: 10px;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }

    .employee-profile-page .asset-form-grid .form-group,
    .employee-profile-page .asset-form-grid .form-group-full {
        min-width: 0;
    }

    .employee-profile-page .asset-form-grid input,
    .employee-profile-page .asset-form-grid select,
    .employee-profile-page .asset-form-grid textarea {
        min-width: 0;
    }

    .employee-profile-page .asset-form-grid .form-group-full {
        grid-column: 1 / -1;
    }

    .employee-profile-page .asset-form-actions {
        margin-top: 10px;
    }

    .employee-profile-page .table-wrap {
        margin-top: 8px;
    }

    .employee-profile-page .table-wrap table {
        background: #ffffff;
    }

    .employee-profile-page .table-wrap th,
    .employee-profile-page .table-wrap td {
        padding: 10px 9px;
        vertical-align: middle;
        color: #111827;
        background: #ffffff;
        border: 1px solid #000000;
    }

    .employee-profile-page .table-wrap th {
        background: #f3f4f6;
        font-weight: 700;
    }

    .employee-profile-page .table-wrap td {
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .employee-profile-page .table-wrap tbody tr {
        cursor: pointer;
        transition: background-color 0.15s ease, color 0.15s ease;
    }

    .employee-profile-page .table-wrap tbody tr:hover td,
    .employee-profile-page .table-wrap tbody tr:active td {
        background: #111827;
        color: #ffffff;
    }

    .employee-profile-page .table-wrap tbody tr:hover td strong,
    .employee-profile-page .table-wrap tbody tr:active td strong {
        color: #ffffff;
    }

    .employee-profile-page .table-wrap tbody tr:hover .badge,
    .employee-profile-page .table-wrap tbody tr:active .badge {
        background: #ffffff;
        color: #111827;
        border: 1px solid #ffffff;
    }

    @media (max-width: 768px) {
        .employee-profile-page .asset-form-grid,
        .employee-profile-page .summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-card employee-profile-page">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">{{ __('employees.show_title') }}</h1>
            <p style="margin:8px 0 0; color:#6b7280;">{{ __('employees.show_subtitle') }}</p>
        </div>

        <div class="actions">
            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">{{ __('employees.edit') }}</a>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">{{ __('employees.back') }}</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="page-card" style="margin-bottom:24px;">
        <div class="page-header">
            <h2 style="margin:0; font-size:24px;">{{ __('employees.section_basic') }}</h2>
        </div>

        <div class="summary-grid">
            <div class="detail-box summary-box">
                <span class="summary-label">{{ __('employees.label_name') }}</span>
                <div class="summary-value">{{ $employee->name ?? '-' }}</div>
            </div>

            <div class="detail-box summary-box">
                <span class="summary-label">{{ __('employees.label_employee_number') }}</span>
                <div class="summary-value">{{ $employee->employee_number ?? '-' }}</div>
            </div>

            <div class="detail-box summary-box">
                <span class="summary-label">{{ __('employees.label_job_title') }}</span>
                <div class="summary-value">{{ $employee->job_title ?? '-' }}</div>
            </div>

            <div class="detail-box summary-box">
                <span class="summary-label">{{ __('employees.label_phone') }}</span>
                <div class="summary-value">{{ $employee->phone ?? '-' }}</div>
            </div>

            <div class="detail-box summary-box">
                <span class="summary-label">{{ __('employees.label_email') }}</span>
                <div class="summary-value">{{ $employee->email ?? '-' }}</div>
            </div>

            <div class="detail-box summary-box">
                <span class="summary-label">{{ __('employees.label_department') }}</span>
                <div class="summary-value">{{ $employee->department->name ?? '-' }}</div>
            </div>

            <div class="detail-box summary-box">
                <span class="summary-label">الراتب الأساسي</span>
                <div class="summary-value">{{ number_format((float) ($employee->salary ?? 0), 2) }}</div>
            </div>

            <div class="detail-box summary-box">
                <span class="summary-label">بدل سكن</span>
                <div class="summary-value">{{ number_format((float) ($employee->housing_allowance ?? 0), 2) }}</div>
            </div>

            <div class="detail-box summary-box">
                <span class="summary-label">بدل مواصلات</span>
                <div class="summary-value">{{ number_format((float) ($employee->transportation_allowance ?? 0), 2) }}</div>
            </div>

            <div class="detail-box summary-box">
                <span class="summary-label">بدل سفر</span>
                <div class="summary-value">{{ number_format((float) ($employee->travel_allowance ?? 0), 2) }}</div>
            </div>

            <div class="detail-box summary-box">
                <span class="summary-label">بدل مخاطر</span>
                <div class="summary-value">{{ number_format((float) ($employee->risk_allowance ?? 0), 2) }}</div>
            </div>

            <div class="detail-box summary-box">
                <span class="summary-label">بدل انتقال</span>
                <div class="summary-value">{{ number_format((float) ($employee->transfer_allowance ?? 0), 2) }}</div>
            </div>

            <div class="detail-box summary-box">
                <span class="summary-label">بدل إضافي</span>
                <div class="summary-value">{{ number_format((float) ($employee->overtime_allowance ?? 0), 2) }}</div>
            </div>

            <div class="detail-box summary-box">
                <span class="summary-label">{{ __('employees.label_passport') }}</span>
                <div class="summary-value">{{ $employee->passport_number ?? '-' }}</div>
            </div>

            <div class="detail-box summary-box">
                <span class="summary-label">{{ __('employees.label_passport_expiry') }}</span>
                <div class="summary-value">{{ $employee->passport_expiry_date ?? '-' }}</div>
            </div>

            <div class="detail-box summary-box">
                <span class="summary-label">{{ __('employees.custody_state_label') }}</span>
                <div class="summary-value">
                    @if($employee->has_custody)
                        <span class="badge badge-green">{{ __('employees.custody_yes_short') }}</span>
                    @else
                        <span class="badge badge-gray">{{ __('employees.custody_no_short') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="page-card" style="margin-bottom:24px;">
        <div class="page-header">
            <h2>{{ __('employees.section_custody') }}</h2>
        </div>

        <form action="{{ route('employees.assets.store', $employee->id) }}" method="POST">
            @csrf

            <div class="asset-form-grid">

                <div class="form-group">
                    <label>{{ __('employees.asset_name') }}</label>
                    <input type="text" name="asset_name" required placeholder="{{ __('employees.asset_placeholder') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('employees.start_date') }}</label>
                    <input type="date" name="start_date" required>
                </div>

                <div class="form-group">
                    <label>{{ __('employees.end_date') }}</label>
                    <input type="date" name="end_date">
                </div>

                <div class="form-group">
                    <label>{{ __('employees.assign_status') }}</label>
                    <select name="status">
                        <option value="active">{{ __('employees.asset_assign_status_active') }}</option>
                        <option value="ended">{{ __('employees.asset_assign_status_ended') }}</option>
                        <option value="lost">{{ __('employees.asset_assign_status_lost') }}</option>
                        <option value="damaged">{{ __('employees.asset_assign_status_damaged') }}</option>
                    </select>
                </div>

                <div class="form-group form-group-full">
                    <label>{{ __('employees.notes') }}</label>
                    <textarea name="notes"></textarea>
                </div>

            </div>

            <div class="asset-form-actions">
                <button class="btn btn-primary">{{ __('employees.add_custody') }}</button>
            </div>
        </form>
    </div>

    @php
        $salaryBase = (float) ($employee->salary ?? 0);
        $hourlyRate = $salaryBase / 240;
        $overtimeHourRate = $hourlyRate * 1.5;
        $overtimeHours = (float) ($payrollAdjustment->overtime_hours ?? 0);
        $overtimeTotal = $overtimeHours * $overtimeHourRate;
        $dailyRate = $salaryBase / 30;
        $leaveDays = (float) ($payrollAdjustment->leave_deduction_days ?? 0);
        $leaveDeductionTotal = $leaveDays * $dailyRate;
    @endphp

    <div class="page-card" style="margin-bottom:24px;">
        <div class="page-header">
            <h2>حسابات مسير الرواتب</h2>
            <p style="color:#94a3b8;">الشهر الحالي: {{ sprintf('%02d/%04d', $currentMonth, $currentYear) }}</p>
        </div>

        <form action="{{ route('employees.payroll-adjustment.save', $employee) }}" method="POST">
            @csrf
            <input type="hidden" name="month" value="{{ $currentMonth }}">
            <input type="hidden" name="year" value="{{ $currentYear }}">
            <div class="asset-form-grid">
                <div class="form-group">
                    <label>ساعات العمل الإضافي</label>
                    <input type="number" step="0.01" min="0" name="overtime_hours" value="{{ old('overtime_hours', $payrollAdjustment->overtime_hours ?? 0) }}" @disabled(!($canEditPayrollAdjustment ?? true))>
                </div>

                <div class="form-group">
                    <label>أيام خصم الإجازات</label>
                    <input type="number" step="0.01" min="0" name="leave_deduction_days" value="{{ old('leave_deduction_days', $payrollAdjustment->leave_deduction_days ?? 0) }}" @disabled(!($canEditPayrollAdjustment ?? true))>
                </div>

                <div class="form-group">
                    <label>خصومات أخرى</label>
                    <input type="number" step="0.01" min="0" name="other_deduction" value="{{ old('other_deduction', $payrollAdjustment->other_deduction ?? 0) }}" @disabled(!($canEditPayrollAdjustment ?? true))>
                </div>

                <div class="form-group form-group-full">
                    <label>ملاحظات</label>
                    <textarea name="notes" @disabled(!($canEditPayrollAdjustment ?? true))>{{ old('notes', $payrollAdjustment->notes) }}</textarea>
                </div>
            </div>

            {{-- hide formulas --}}
            {{-- تم إخفاء المعادلات بناءً على طلب العميل --}}

            <div class="asset-form-actions">
                @if($canEditPayrollAdjustment ?? true)
                    <button type="submit" class="btn btn-primary">حفظ حسابات المسير</button>
                @else
                    <p style="color:#6b7280; margin:0;">{{ __('employees.payroll_cannot_edit_approved') }}</p>
                @endif
            </div>
        </form>
    </div>

    <div class="page-card">
        <div class="page-header">
            <h2>{{ __('employees.custody_log_title') }}</h2>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('employees.th_asset') }}</th>
                        <th>{{ __('employees.th_serial') }}</th>
                        <th>{{ __('employees.th_start') }}</th>
                        <th>{{ __('employees.assign_status') }}</th>
                        <th>{{ __('employees.th_action') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($employee->assets as $asset)
                    <tr>
                        <td>{{ $asset->asset_name }}</td>
                        <td>
                            <strong>{{ $asset->serial_number }}</strong>
                        </td>
                        <td>{{ $asset->start_date }}</td>
                        <td>{{ __('employees.asset_assign_status_'.$asset->status) }}</td>
                        <td>
                            <form action="{{ route('employees.assets.destroy', $asset->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">{{ __('employees.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="page-card" style="margin-top:16px;">
        <div class="page-header">
            <h2>العهدة (الأصول)</h2>
            <p style="color:#6b7280;">سجل الأصول الحالية والمرجعة لهذا الموظف</p>
        </div>

        @php
            $activeAssignments = $employee->assetAssignments->filter(fn ($a) => $a->status === 'assigned' && $a->returned_at === null);
            $assignmentHistory = $employee->assetAssignments->sortByDesc('assigned_at');
        @endphp

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('employees.th_asset') }}</th>
                        <th>{{ __('employees.th_serial') }}</th>
                        <th>نوع الأصل</th>
                        <th>تاريخ التسليم</th>
                        <th>تاريخ الإرجاع</th>
                        <th>{{ __('employees.assign_status') }}</th>
                        <th>{{ __('employees.notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignmentHistory as $assignment)
                        <tr>
                            <td>{{ optional($assignment->asset)->name ?? '-' }}</td>
                            <td>{{ optional($assignment->asset)->serial_number ?? '-' }}</td>
                            <td>
                                @if(optional($assignment->asset)->asset_type === 'vehicle')
                                    {{ __('assets.asset_type_vehicle') }}
                                @elseif(optional($assignment->asset)->asset_type)
                                    {{ __('assets.asset_type_general') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ optional($assignment->assigned_at)->format('Y-m-d H:i') ?? '-' }}</td>
                            <td>{{ optional($assignment->returned_at)->format('Y-m-d H:i') ?? '-' }}</td>
                            <td>
                                @if($assignment->status === 'assigned' && $assignment->returned_at === null)
                                    <span class="badge badge-green">مع الموظف</span>
                                @else
                                    <span class="badge badge-gray">تم الإرجاع</span>
                                @endif
                            </td>
                            <td>{{ $assignment->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-row">لا توجد سجلات عهدة أصول لهذا الموظف</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activeAssignments->isNotEmpty())
            <div style="margin-top:10px;">
                <span class="badge badge-blue">العهدة الحالية: {{ $activeAssignments->count() }}</span>
            </div>
        @endif
    </div>

</div>
@endsection
