@extends('layouts.app')

@section('content')
<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">إضافة موظف جديد</h1>
            <p style="margin:8px 0 0; color:#6b7280;">إدخال بيانات الموظف الأساسية وإلحاقه بالقسم المناسب</p>
        </div>

        <a href="{{ route('employees.index') }}" class="btn btn-secondary">
            رجوع
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
        <form action="{{ route('employees.store') }}" method="POST">
            @csrf

            <div class="form-grid">

                <div class="form-group">
                    <label>اسم الموظف</label>
                    <input type="text" name="name" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label>الرقم الوظيفي</label>
                    <input type="text" name="employee_number" value="{{ old('employee_number') }}">
                </div>

                <div class="form-group">
                    <label>المسمى الوظيفي</label>
                    <input type="text" name="job_title" value="{{ old('job_title') }}">
                </div>

                <div class="form-group">
                    <label>الجوال</label>
                    <input type="text" name="phone" value="{{ old('phone') }}">
                </div>

                <div class="form-group">
                    <label>البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label>القسم</label>
                    <select name="department_id">
                        <option value="">-- اختر القسم --</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group form-group-full">
                    <label>العنوان</label>
                    <textarea name="address">{{ old('address') }}</textarea>
                </div>

                <div class="form-group">
                    <label>تاريخ التوظيف</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date') }}">
                </div>

                <div class="form-group">
                    <label>الراتب</label>
                    <input type="number" step="0.01" name="salary" value="{{ old('salary') }}">
                </div>

                <div class="form-group">
                    <label>الحالة</label>
                    <select name="status" required>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>inactive</option>
                        <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>suspended</option>
                    </select>
                </div>

                <!-- 🔥 الحقل الجديد -->
                <div class="form-group">
                    <label>تاريخ انتهاء الإقامة</label>
                    <input type="date" name="residency_expiry_date" value="{{ old('residency_expiry_date') }}">
                </div>

            </div>

            <div class="form-actions" style="margin-top: 10px;">
                <button type="submit" class="btn btn-primary">حفظ</button>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>

</div>
@endsection