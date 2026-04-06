@extends('layouts.app')

@section('content')
<div class="page-card">

    <div class="page-header">
        <h1 class="page-title">تعديل أمر الإنتاج</h1>
    </div>

    <form action="{{ route('factory.update', $order->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-grid">

            <div class="form-group">
                <label>رقم الأمر</label>
                <input type="text" name="order_number" value="{{ old('order_number', $order->order_number) }}" required>
            </div>

            <div class="form-group">
                <label>اسم المنتج</label>
                <input type="text" name="product_name" value="{{ old('product_name', $order->product_name) }}" required>
            </div>

            <div class="form-group">
                <label>الكمية المطلوبة</label>
                <input type="number" step="0.01" name="planned_quantity" value="{{ old('planned_quantity', $order->planned_quantity) }}" required>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">تحديث</button>
            <a href="{{ route('factory.index') }}" class="btn btn-secondary">رجوع</a>
        </div>

    </form>
</div>
@endsection