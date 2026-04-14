@extends('layouts.app')

@section('content')

<div class="page-card" dir="rtl" style="text-align:right;">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 class="page-title">مشتريات المشاريع</h1>
            <p style="color:#6b7280;">إضافة شراء أو صيانة لمشروع</p>
        </div>

        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
            رجوع
        </a>
    </div>

    @if($errors->any())
        <div class="alert-danger">
            <ul style="margin:0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('purchases.store') }}" method="POST">
        @csrf

        <div class="form-grid">

            <div class="form-group">
                <label>النوع</label>
                <select name="type" required>
                    <option value="purchase">شراء</option>
                    <option value="repair">صيانة</option>
                </select>
            </div>

            <div class="form-group">
                <label>اسم الصنف</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="مثال: إسمنت / مضخة / صيانة">
            </div>

            <div class="form-group">
                <label>التصنيف</label>
                <input type="text" name="category" value="{{ old('category') }}" placeholder="مثال: مواد / معدات">
            </div>

            <div class="form-group">
                <label>الكمية</label>
                <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1">
            </div>

            <div class="form-group">
                <label>المشروع</label>
                <select name="project_id" required>
                    <option value="">اختر المشروع</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>التاريخ</label>
                <input type="date" name="date" value="{{ old('date') }}">
            </div>

            <div class="form-group">
                <label>المورد</label>
                <input type="text" name="vendor" value="{{ old('vendor') }}" placeholder="اسم المورد">
            </div>

            <div class="form-group">
                <label>التكلفة الإجمالية</label>
                <input type="number" step="0.01" name="total_cost" value="{{ old('total_cost') }}" placeholder="0.00">
            </div>

            <div class="form-group">
                <label>تكلفة الوحدة</label>
                <input type="number" step="0.01" name="unit_cost" value="{{ old('unit_cost') }}" placeholder="0.00">
            </div>

            <div class="form-group form-group-full">
                <label>ملاحظات</label>
                <textarea name="notes" rows="3">{{ old('notes') }}</textarea>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                حفظ
            </button>
        </div>
    </form>

</div>

@endsection