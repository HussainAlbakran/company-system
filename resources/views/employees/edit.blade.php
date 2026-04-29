@extends('layouts.app')

@section('page_title', __('employees.edit_title'))
@section('page_subtitle', __('employees.edit_subtitle'))

@section('content')
<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">{{ __('employees.edit_title') }}</h1>
            <p style="margin:8px 0 0; color:#6b7280;">{{ __('employees.edit_subtitle') }}</p>
        </div>

        <a href="{{ route('employees.show', $employee) }}" class="btn btn-secondary">
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
        <form action="{{ route('employees.update', $employee) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">

                <div class="form-group">
                    <label>{{ __('employees.name') }}</label>
                    <input type="text" name="name" value="{{ old('name', $employee->name) }}" required>
                </div>

                <div class="form-group">
                    <label>{{ __('employees.employee_number') }}</label>
                    <input type="text" name="employee_number" value="{{ old('employee_number', $employee->employee_number) }}">
                </div>

                <div class="form-group">
                    <label>{{ __('employees.job_title') }}</label>
                    <input type="text" name="job_title" value="{{ old('job_title', $employee->job_title) }}">
                </div>

                <div class="form-group">
                    <label>{{ __('employees.phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}">
                </div>

                <div class="form-group">
                    <label>{{ __('employees.email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $employee->email) }}">
                </div>

                <div class="form-group">
                    <label>{{ __('employees.department') }}</label>
                    <select name="department_id">
                        <option value="">{{ __('employees.select_department') }}</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}"
                                {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group form-group-full">
                    <label>{{ __('employees.address') }}</label>
                    <textarea name="address">{{ old('address', $employee->address) }}</textarea>
                </div>

                <div class="form-group">
                    <label>{{ __('employees.hire_date') }}</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', $employee->hire_date) }}">
                </div>

                <div class="form-group">
                    <label>{{ __('employees.salary') }}</label>
                    <input type="number" step="0.01" name="salary" value="{{ old('salary', $employee->salary) }}">
                </div>

                <div class="form-group">
                    <label>بدل سكن</label>
                    <input type="number" step="0.01" name="housing_allowance" value="{{ old('housing_allowance', $employee->housing_allowance ?? 0) }}">
                </div>

                <div class="form-group">
                    <label>بدل مواصلات</label>
                    <input type="number" step="0.01" name="transportation_allowance" value="{{ old('transportation_allowance', $employee->transportation_allowance ?? 0) }}">
                </div>

                <div class="form-group">
                    <label>بدل سفر</label>
                    <input type="number" step="0.01" name="travel_allowance" value="{{ old('travel_allowance', $employee->travel_allowance ?? 0) }}">
                </div>

                <div class="form-group">
                    <label>بدل مخاطر</label>
                    <input type="number" step="0.01" name="risk_allowance" value="{{ old('risk_allowance', $employee->risk_allowance ?? 0) }}">
                </div>

                <div class="form-group">
                    <label>بدل انتقال</label>
                    <input type="number" step="0.01" name="transfer_allowance" value="{{ old('transfer_allowance', $employee->transfer_allowance ?? 0) }}">
                </div>

                <div class="form-group">
                    <label>بدل إضافي</label>
                    <input type="number" step="0.01" name="overtime_allowance" value="{{ old('overtime_allowance', $employee->overtime_allowance ?? 0) }}">
                </div>

                <div class="form-group">
                    <label>{{ __('employees.status') }}</label>
                    <select name="status" required>
                        <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>{{ __('employees.status_active') }}</option>
                        <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>{{ __('employees.status_inactive') }}</option>
                        <option value="suspended" {{ old('status', $employee->status) == 'suspended' ? 'selected' : '' }}>{{ __('employees.status_suspended') }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>{{ __('employees.residency_expiry') }}</label>
                    <input type="date" name="residency_expiry_date"
                        value="{{ old('residency_expiry_date', $employee->residency_expiry_date) }}">
                </div>

                <div class="form-group">
                    <label>{{ __('employees.passport_number') }}</label>
                    <input type="text" name="passport_number"
                        value="{{ old('passport_number', $employee->passport_number) }}">
                </div>

                <div class="form-group">
                    <label>{{ __('employees.passport_expiry') }}</label>
                    <input type="date" name="passport_expiry_date"
                        value="{{ old('passport_expiry_date', $employee->passport_expiry_date) }}">
                </div>

            </div>

            <div class="form-actions" style="margin-top: 10px;">
                <button type="submit" class="btn btn-primary">{{ __('employees.update') }}</button>
                <a href="{{ route('employees.show', $employee) }}" class="btn btn-secondary">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>

</div>
@endsection
