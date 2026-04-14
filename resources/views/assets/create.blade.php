@extends('layouts.app')

@section('content')

<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 class="page-title">إضافة أصل جديد</h1>
            <p style="color:#6b7280;">تسجيل أصل جديد في النظام</p>
        </div>

        <a href="{{ route('assets.index') }}" class="btn btn-secondary">
            رجوع
        </a>
    </div>

    @if($errors->any())
        <div class="alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('assets.store') }}" method="POST">
        @csrf

        <div class="form-grid">

            <div class="form-group">
                <label>اسم الأصل</label>
                <input type="text" name="name" required placeholder="مثال: لابتوب / سيارة / ماكينة">
            </div>

            <div class="form-group">
                <label>الكمية</label>
                <input type="number" name="quantity" value="1" min="1" required>
            </div>

            <div class="form-group">
                <label>الحالة</label>
                <select name="status" required>
                    <option value="available">متاح</option>
                    <option value="assigned">مُسلم</option>
                    <option value="maintenance">صيانة</option>
                </select>
            </div>

            <div class="form-group">
                <label>تاريخ الإضافة</label>
                <input type="date" name="purchase_date">
            </div>

            <div class="form-group form-group-full">
                <label>ملاحظات</label>
                <textarea name="notes"></textarea>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                حفظ الأصل
            </button>
        </div>

    </form>

</div>

@endsection