@extends('layouts.app')

@section('content')
<div class="page-card">
    <div class="page-header">
        <h1 class="page-title">تعديل مشروع | Edit Project</h1>
    </div>

    <form action="{{ route('engineering-projects.update', $project->id) }}" method="POST" enctype="multipart/form-data" class="form-card">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group">
                <label>اسم المشروع | Project Name</label>
                <input type="text" name="name" value="{{ old('name', $project->name) }}" required>
            </div>

            <div class="form-group">
                <label>الموظف المسؤول | Responsible Employee</label>
                <select name="responsible_employee_id">
                    <option value="">اختر موظفًا</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('responsible_employee_id', $project->responsible_employee_id) == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>تاريخ البداية | Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date', $project->start_date) }}" required>
            </div>

            <div class="form-group">
                <label>تاريخ النهاية | End Date</label>
                <input type="date" name="end_date" value="{{ old('end_date', $project->end_date) }}" required>
            </div>

            <div class="form-group">
                <label>نسبة الإنجاز | Progress %</label>
                <input type="number" name="progress_percentage" min="0" max="100" value="{{ old('progress_percentage', $project->progress_percentage) }}" required>
            </div>

            <div class="form-group">
                <label>قيمة المشروع | Project Value</label>
                <input type="number" step="0.01" name="project_value" min="0" value="{{ old('project_value', $project->project_value) }}" required>
            </div>

            <div class="form-group">
                <label>المصاريف | Expenses</label>
                <input type="number" step="0.01" name="expenses" min="0" value="{{ old('expenses', $project->expenses) }}" required>
            </div>

            <div class="form-group">
                <label>الحالة | Status</label>
                <input type="text" name="status" value="{{ old('status', $project->status) }}" required>
            </div>

            <div class="form-group form-group-full">
                <label>الوصف | Description</label>
                <textarea name="description" rows="4">{{ old('description', $project->description) }}</textarea>
            </div>

            <div class="form-group form-group-full">
                <label>ملاحظات | Notes</label>
                <textarea name="notes" rows="4">{{ old('notes', $project->notes) }}</textarea>
            </div>

            <div class="form-group form-group-full">
                <label>ملف PDF جديد | New PDF</label>
                <input type="file" name="project_pdf" accept=".pdf">
                @if($project->project_pdf)
                    <p style="margin-top:8px;">
                        الملف الحالي موجود | Current file available
                    </p>
                @endif
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">تحديث | Update</button>
            <a href="{{ route('engineering-projects.index') }}" class="btn btn-secondary">رجوع | Back</a>
        </div>
    </form>
</div>
@endsection