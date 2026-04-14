@extends('layouts.app')

@section('content')

<div class="page-card" dir="rtl" style="text-align:right;">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">تعديل بيانات المستودع</h1>
            <p style="margin:8px 0 0; color:#6b7280;">
                تعديل العنصر في قسم {{ $sectionName }}
            </p>
        </div>

        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            رجوع
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert-danger">
            <ul style="margin:0; padding-right:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('warehouse.update', $item->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-grid">

            <div class="form-group">
                <label>الاسم</label>
                <input type="text" name="name" value="{{ old('name', $item->name) }}">
            </div>

            <div class="form-group">
                <label>الكمية</label>
                <input type="text" name="quantity" value="{{ old('quantity', $item->quantity) }}">
            </div>

            <div class="form-group">
                <label>الوحدة</label>
                <input type="text" name="unit" value="{{ old('unit', $item->unit) }}">
            </div>

            <div class="form-group form-group-full">
                <label>ملاحظات</label>
                <textarea name="notes" rows="4">{{ old('notes', $item->notes) }}</textarea>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                حفظ التعديل
            </button>
        </div>
    </form>

</div>

@endsection