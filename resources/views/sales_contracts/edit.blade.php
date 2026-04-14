@extends('layouts.app')

@section('content')

<div class="page-card">
    <div class="page-header">
        <h2>تعديل العقد</h2>
        <p>تحديث بيانات العقد والمشروع المرتبط به</p>
    </div>

    <form action="{{ route('sales-contracts.update', $contract->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-grid">

            <div class="form-group">
                <label>رقم العقد</label>
                <input type="text" name="contract_no" value="{{ old('contract_no', $contract->contract_no) }}" required>
            </div>

            <div class="form-group">
                <label>تاريخ العقد</label>
                <input type="date" name="contract_date" value="{{ old('contract_date', $contract->contract_date) }}" required>
            </div>

            <div class="form-group">
                <label>اسم العميل</label>
                <input type="text" name="client_name" value="{{ old('client_name', $contract->client_name) }}" required>
            </div>

            <div class="form-group">
                <label>المقاول الرئيسي</label>
                <input type="text" name="main_contractor" value="{{ old('main_contractor', $contract->main_contractor) }}">
            </div>

            <div class="form-group">
                <label>اسم المشروع</label>
                <input type="text" name="project_name" value="{{ old('project_name', $contract->project_name) }}" required>
            </div>

            <div class="form-group">
                <label>موقع المشروع</label>
                <input type="text" name="project_location" value="{{ old('project_location', $contract->project_location) }}">
            </div>

            <div class="form-group">
                <label>قيمة المشروع</label>
                <input type="number" step="0.01" name="project_value" value="{{ old('project_value', $contract->project_value) }}">
            </div>

            <div class="form-group">
                <label>مدة المشروع (بالأيام)</label>
                <input type="number" name="project_duration" value="{{ old('project_duration', $contract->project_duration) }}">
            </div>

            <div class="form-group">
                <label>تاريخ البداية المتوقع</label>
                <input type="date" name="expected_start_date" value="{{ old('expected_start_date', $contract->expected_start_date) }}">
            </div>

            <div class="form-group">
                <label>التاريخ الفعلي للمشروع</label>
                <input type="date" name="actual_start_date" value="{{ old('actual_start_date', $contract->actual_start_date) }}">
            </div>

            <div class="form-group form-group-full">
                <label>وصف المشروع</label>
                <textarea name="description">{{ old('description', $contract->description) }}</textarea>
            </div>

            <div class="form-group form-group-full">
                <label>ملاحظات</label>
                <textarea name="notes">{{ old('notes', $contract->notes) }}</textarea>
            </div>

            <div class="form-group form-group-full">
                <label>ملف العقد الجديد</label>
                <input type="file" name="contract_file">
            </div>

        </div>

        <div class="actions-row" style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">حفظ التعديل</button>
            <a href="{{ route('sales-contracts.show', $contract->id) }}" class="btn btn-secondary">رجوع</a>
        </div>
    </form>
</div>

@endsection