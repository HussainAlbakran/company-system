@extends('layouts.app')

@section('content')

<div class="page-card">

    <div class="page-header">
        <h2>تعديل عملية شراء / إصلاح</h2>
        <p>تحديث بيانات العملية المرتبطة بالمشروع</p>
    </div>

    @if($errors->any())
        <div class="alert-danger">
            <ul style="margin:0; padding-right:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('purchases.update', $purchase->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-grid">

            <div class="form-group">
                <label>المشروع</label>
                <select name="project_id" required>
                    <option value="">اختر المشروع</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}"
                            {{ old('project_id', $purchase->project_id) == $project->id ? 'selected' : '' }}>
                            {{ $project->project_code }} - {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>نوع العملية</label>
                <select name="type" required>
                    <option value="purchase" {{ old('type', $purchase->type) == 'purchase' ? 'selected' : '' }}>شراء</option>
                    <option value="repair" {{ old('type', $purchase->type) == 'repair' ? 'selected' : '' }}>إصلاح</option>
                </select>
            </div>

            <div class="form-group">
                <label>اسم البند</label>
                <input type="text" name="title" value="{{ old('title', $purchase->title) }}" required>
            </div>

            <div class="form-group">
                <label>التكلفة</label>
                <input type="number" step="0.01" name="cost" value="{{ old('cost', $purchase->cost) }}" required>
            </div>

            <div class="form-group">
                <label>المورد / الجهة</label>
                <input type="text" name="vendor" value="{{ old('vendor', $purchase->vendor) }}">
            </div>

            <div class="form-group">
                <label>تاريخ العملية</label>
                <input type="date" name="purchase_date" value="{{ old('purchase_date', optional($purchase->purchase_date)->format('Y-m-d') ?? $purchase->purchase_date) }}">
            </div>

            <div class="form-group form-group-full">
                <label>الوصف</label>
                <textarea name="description">{{ old('description', $purchase->description) }}</textarea>
            </div>

            <div class="form-group form-group-full">
                <label>ملاحظات</label>
                <textarea name="notes">{{ old('notes', $purchase->notes) }}</textarea>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">حفظ التعديل</button>
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">رجوع</a>
        </div>

    </form>

</div>

@endsection