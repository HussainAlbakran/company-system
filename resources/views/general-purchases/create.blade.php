@extends('layouts.app')

@section('content')

<div class="page-card" dir="rtl" style="text-align:right;">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 class="page-title">المشتريات العامة</h1>
            <p style="color:#6b7280;">شراء الأصول والصيانة العامة</p>
        </div>

        <a href="{{ route('general-purchases.index') }}" class="btn btn-secondary">
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

    <form action="{{ route('general-purchases.store') }}" method="POST">
        @csrf

        <div class="form-grid">

            <div class="form-group">
                <label>النوع</label>
                <select name="type" id="purchaseType" onchange="toggleGeneralPurchaseFields()" required>
                    <option value="asset_purchase">شراء أصل</option>
                    <option value="general_maintenance">صيانة عامة</option>
                </select>
            </div>

            <div class="form-group">
                <label>اسم الصنف / الصيانة</label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="أدخل اسم الصنف أو الصيانة">
            </div>

            <div class="form-group">
                <label>التصنيف</label>
                <input type="text" name="description" value="{{ old('description') }}" placeholder="التصنيف أو القسم">
            </div>

            <div class="form-group asset-only-field">
                <label>الكمية</label>
                <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}">
            </div>

            <div class="form-group">
                <label>التكلفة</label>
                <input type="number" step="0.01" name="cost" value="{{ old('cost') }}" required placeholder="0.00">
            </div>

            <div class="form-group">
                <label>المورد</label>
                <input type="text" name="vendor" value="{{ old('vendor') }}" placeholder="اسم المورد أو مقدم الخدمة">
            </div>

            <div class="form-group">
                <label>التاريخ</label>
                <input type="date" name="purchase_date" value="{{ old('purchase_date') }}">
            </div>

            <div class="form-group form-group-full asset-only-field">
                <label>الرقم التسلسلي</label>
                <input type="text" name="serial_number" value="{{ old('serial_number') }}" placeholder="الرقم التسلسلي للأصل">
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

<script>
function toggleGeneralPurchaseFields() {
    const type = document.getElementById('purchaseType').value;
    const assetFields = document.querySelectorAll('.asset-only-field');

    assetFields.forEach(field => {
        field.style.display = type === 'asset_purchase' ? 'block' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', function () {
    toggleGeneralPurchaseFields();
});
</script>

@endsection