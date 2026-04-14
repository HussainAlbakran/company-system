@extends('layouts.app')

@section('content')

<div class="page-card">

    <div class="page-header">
        <h2>تقديم إجازة</h2>
        <p>تقديم طلب إجازة جديد عبر النظام</p>
    </div>

    <form action="{{ route('leaves.store') }}" method="POST">
        @csrf

        <div class="form-grid">

            <div class="form-group">
                <label>الموظف</label>
                <select name="employee_id" required>
                    <option value="">-- اختر الموظف --</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>تاريخ بداية الإجازة</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" required>
            </div>

            <div class="form-group">
                <label>تاريخ نهاية الإجازة</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" required>
            </div>

            <div class="form-group form-group-full">
                <label>سبب الإجازة</label>
                <textarea name="reason" rows="4">{{ old('reason') }}</textarea>
            </div>

        </div>

        <div class="actions-row" style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">
                تقديم الطلب
            </button>

            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                رجوع
            </a>
        </div>
    </form>

</div>

@endsection