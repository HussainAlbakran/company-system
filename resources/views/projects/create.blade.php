@extends('layouts.app')

@section('content')
<div class="page-card">

    <div class="page-header">
        <h1 class="page-title">➕ إضافة مشروع</h1>
    </div>

    {{-- ERRORS --}}
    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom:15px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('engineering.projects.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-grid">

            <div class="form-group">
                <label>اسم المشروع</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label>الموظف المسؤول</label>
                <select name="responsible_employee_id">
                    <option value="">اختر موظف</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" 
                            {{ old('responsible_employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>تاريخ البداية</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" required>
            </div>

            <div class="form-group">
                <label>تاريخ النهاية</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" required>
            </div>

            <div class="form-group">
                <label>نسبة الإنجاز</label>
                <input type="number" name="progress_percentage" min="0" max="100"
                       value="{{ old('progress_percentage', 0) }}">
            </div>

            <div class="form-group">
                <label>قيمة المشروع</label>
                <input type="number" step="0.01" name="project_value"
                       value="{{ old('project_value', 0) }}">
            </div>

            <div class="form-group">
                <label>المصاريف</label>
                <input type="number" step="0.01" name="expenses"
                       value="{{ old('expenses', 0) }}">
            </div>

            <div class="form-group">
                <label>الحالة</label>
                <input type="text" name="status"
                       value="{{ old('status', 'ongoing') }}">
            </div>

            <div class="form-group form-group-full">
                <label>الوصف</label>
                <textarea name="description">{{ old('description') }}</textarea>
            </div>

            <div class="form-group form-group-full">
                <label>ملاحظات</label>
                <textarea name="notes">{{ old('notes') }}</textarea>
            </div>

            <div class="form-group form-group-full">
                <label>ملف PDF</label>
                <input type="file" name="project_pdf" accept="application/pdf">
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 حفظ</button>
            <a href="{{ route('engineering.projects.index') }}" class="btn btn-secondary">رجوع</a>
        </div>

    </form>

</div>
@endsection