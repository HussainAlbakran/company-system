@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">إضافة أمر إنتاج</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('factory.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">رقم أمر الإنتاج</label>
                    <input type="text" name="order_number" class="form-control" value="{{ old('order_number') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">اسم المنتج</label>
                    <input type="text" name="product_name" class="form-control" value="{{ old('product_name') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">الكمية المطلوبة</label>
                    <input type="number" step="0.01" name="planned_quantity" class="form-control" value="{{ old('planned_quantity') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">تاريخ بداية الإنتاج</label>
                    <input type="date" name="production_start_date" class="form-control" value="{{ old('production_start_date') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">تاريخ النهاية المتوقع</label>
                    <input type="date" name="expected_end_date" class="form-control" value="{{ old('expected_end_date') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">الهدف اليومي</label>
                    <input type="number" step="0.01" name="daily_target" class="form-control" value="{{ old('daily_target') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">ملاحظات</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-success">حفظ</button>
                <a href="{{ route('factory.index') }}" class="btn btn-secondary">رجوع</a>
            </form>
        </div>
    </div>
</div>
@endsection