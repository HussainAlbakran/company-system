@extends('layouts.app')

@php
    $accountRoleKeys = [
        'sales_manager', 'sales', 'engineering_manager', 'engineer',
        'procurement_manager', 'procurement', 'hr_manager', 'hr', 'operations_manager',
    ];
@endphp

@section('page_title', __('employees.create_title'))
@section('page_subtitle', __('employees.create_subtitle'))

@section('content')
<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">{{ __('employees.create_title') }}</h1>
            <p style="margin:8px 0 0; color:#6b7280;">{{ __('employees.create_subtitle') }}</p>
        </div>

        <a href="{{ route('employees.index') }}" class="btn btn-secondary">
            {{ __('employees.back') }}
        </a>
    </div>

    @if ($errors->any())
        <div class="alert-danger">
            <ul style="margin:0; padding-right:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="page-card">
        <form action="{{ route('employees.store') }}" method="POST" data-autofill-form-key="employees" data-autofill-endpoint="{{ route('documents.parse') }}">
            @csrf

            <div class="form-group form-group-full" style="margin-bottom: 14px;">
                <label>{{ __('employees.smart_import_label') }}</label>
                <input type="file" name="document" accept=".pdf,.xlsx,.csv,.jpg,.jpeg,.png,.webp" data-autofill-document-input>
                <small data-autofill-status style="display:block; margin-top:6px; color:#94a3b8;"></small>
            </div>

            <div class="form-grid">

                <div class="form-group">
                    <label>{{ __('employees.name') }}</label>
                    <input type="text" name="name" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label>{{ __('employees.employee_number') }}</label>
                    <input type="text" name="employee_number" value="{{ old('employee_number') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('employees.job_title') }}</label>
                    <input type="text" name="job_title" value="{{ old('job_title') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('employees.phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('employees.email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('employees.department') }}</label>
                    <select name="department_id">
                        <option value="">{{ __('employees.select_department') }}</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group form-group-full">
                    <label>{{ __('employees.address') }}</label>
                    <textarea name="address">{{ old('address') }}</textarea>
                </div>

                <div class="form-group">
                    <label>{{ __('employees.hire_date') }}</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('employees.salary') }}</label>
                    <input type="number" step="0.01" name="salary" value="{{ old('salary') }}">
                </div>

                <div class="form-group">
                    <label>بدل سكن</label>
                    <input type="number" step="0.01" name="housing_allowance" value="{{ old('housing_allowance', 0) }}">
                </div>

                <div class="form-group">
                    <label>بدل مواصلات</label>
                    <input type="number" step="0.01" name="transportation_allowance" value="{{ old('transportation_allowance', 0) }}">
                </div>

                <div class="form-group">
                    <label>بدل سفر</label>
                    <input type="number" step="0.01" name="travel_allowance" value="{{ old('travel_allowance', 0) }}">
                </div>

                <div class="form-group">
                    <label>بدل مخاطر</label>
                    <input type="number" step="0.01" name="risk_allowance" value="{{ old('risk_allowance', 0) }}">
                </div>

                <div class="form-group">
                    <label>بدل انتقال</label>
                    <input type="number" step="0.01" name="transfer_allowance" value="{{ old('transfer_allowance', 0) }}">
                </div>

                <div class="form-group">
                    <label>بدل إضافي</label>
                    <input type="number" step="0.01" name="overtime_allowance" value="{{ old('overtime_allowance', 0) }}">
                </div>

                <div class="form-group">
                    <label>{{ __('employees.status') }}</label>
                    <select name="status" required>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>{{ __('employees.status_active') }}</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>{{ __('employees.status_inactive') }}</option>
                        <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>{{ __('employees.status_suspended') }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>{{ __('employees.leave_balance_days') }}</label>
                    <input type="number" name="leave_balance" value="{{ old('leave_balance', 26) }}">
                </div>

                <div class="form-group">
                    <label>{{ __('employees.residency_expiry') }}</label>
                    <input type="date" name="residency_expiry_date" value="{{ old('residency_expiry_date') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('employees.passport_number') }}</label>
                    <input type="text" name="passport_number" value="{{ old('passport_number') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('employees.passport_expiry') }}</label>
                    <input type="date" name="passport_expiry_date" value="{{ old('passport_expiry_date') }}">
                </div>

                <div class="form-group form-group-full">
                    <label style="display:flex; align-items:center; gap:8px;">
                        <input type="checkbox" name="create_system_account" value="1" {{ old('create_system_account') ? 'checked' : '' }} id="create_system_account_toggle" style="width:auto;">
                        {{ __('employees.create_system_account') }}
                    </label>
                </div>

                <div id="system-account-fields" class="form-group form-group-full" style="{{ old('create_system_account') ? '' : 'display:none;' }}">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>{{ __('employees.account_name') }}</label>
                            <input type="text" name="account_name" value="{{ old('account_name') }}">
                        </div>

                        <div class="form-group">
                            <label>{{ __('employees.account_email') }}</label>
                            <input type="email" name="account_email" value="{{ old('account_email') }}">
                        </div>

                        <div class="form-group">
                            <label>{{ __('employees.account_password') }}</label>
                            <input type="password" name="account_password">
                        </div>

                        <div class="form-group">
                            <label>{{ __('employees.account_role') }}</label>
                            <select name="account_role">
                                <option value="">{{ __('employees.select_role') }}</option>
                                @foreach($accountRoleKeys as $roleKey)
                                <option value="{{ $roleKey }}" {{ old('account_role') == $roleKey ? 'selected' : '' }}>{{ __('roles.'.$roleKey) }}</option>
                                @endforeach
                            </select>
                            <small style="display:block; margin-top:6px; color:#cbd5e1;">
                                {{ __('employees.account_role_restriction_note') }}
                            </small>
                        </div>
                    </div>
                </div>

            </div>

            <div class="form-actions" style="margin-top: 10px;">
                <button type="submit" class="btn btn-primary">{{ __('employees.save') }}</button>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>

</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('create_system_account_toggle');
    const fields = document.getElementById('system-account-fields');
    if (!toggle || !fields) return;
    const sync = () => {
        fields.style.display = toggle.checked ? '' : 'none';
    };
    toggle.addEventListener('change', sync);
    sync();
});
</script>
@endsection
