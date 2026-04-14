@extends('layouts.app')

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 class="page-title">تفاصيل مشتريات العقد</h1>
            <p style="margin:8px 0 0; color:#6b7280;">عرض كامل لعملية الشراء المرتبطة بالعقد</p>
        </div>
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">رجوع</a>
    </div>

    <div class="form-grid">
        <div class="detail-box"><strong>المشروع</strong><br>{{ $purchase->project->name ?? '-' }}</div>
        <div class="detail-box"><strong>البند</strong><br>{{ $purchase->title }}</div>
        <div class="detail-box"><strong>النوع</strong><br>{{ $purchase->type ?? '-' }}</div>
        <div class="detail-box"><strong>الكمية</strong><br>{{ $purchase->quantity ?? 1 }}</div>
        <div class="detail-box"><strong>التكلفة</strong><br>{{ number_format((float) $purchase->cost, 2) }}</div>
        <div class="detail-box"><strong>المورد</strong><br>{{ $purchase->vendor ?? '-' }}</div>
        <div class="detail-box"><strong>تاريخ الشراء</strong><br>{{ $purchase->purchase_date ?? '-' }}</div>
        <div class="detail-box"><strong>أنشئ بواسطة</strong><br>{{ $purchase->creator->name ?? '-' }}</div>
    </div>

    <div class="page-card" style="margin-top:20px;">
        <h3 style="margin-top:0;">الوصف</h3>
        <p style="margin:0;">{{ $purchase->description ?? '-' }}</p>
    </div>

    <div class="page-card" style="margin-top:20px;">
        <h3 style="margin-top:0;">ملاحظات</h3>
        <p style="margin:0;">{{ $purchase->notes ?? '-' }}</p>
    </div>
</div>
@endsection
