@extends('layouts.app')

@section('content')

<div class="page-card">

    <div class="page-header">
        <h1 class="page-title">اعتماد المشتريات</h1>
        <p style="color:#6b7280;">اعتماد عمليات الشراء أو الإصلاح قبل التنفيذ</p>
    </div>

    <form action="{{ route('purchases.store') }}" method="POST">
        @csrf

        <div class="form-grid">

            {{-- Project --}}
            <div class="form-group">
                <label>المشروع</label>
                <select name="project_id" required>
                    <option value="">اختر المشروع</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">
                            {{ $project->project_code }} - {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Type --}}
            <div class="form-group">
                <label>النوع</label>
                <select name="type" id="type" onchange="toggleFields()" required>
                    <option value="purchase">شراء</option>
                    <option value="repair">إصلاح</option>
                </select>
            </div>

            {{-- 🔹 Purchase Fields --}}
            <div class="form-group purchase-field">
                <label>اسم البند</label>
                <input type="text" name="title" placeholder="{{ __('purchases.placeholder_purchase_item') }}">
            </div>

            <div class="form-group purchase-field">
                <label>الكمية</label>
                <input type="number" name="quantity" min="1">
            </div>

            {{-- 🔹 Repair Field --}}
            <div class="form-group repair-field" style="display:none;">
                <label>عنصر الإصلاح</label>
                <input type="text" name="title" placeholder="{{ __('purchases.placeholder_repair_item') }}">
            </div>

            {{-- Cost --}}
            <div class="form-group">
                <label>التكلفة</label>
                <input type="number" step="0.01" name="cost" required>
            </div>

            {{-- Date --}}
            <div class="form-group">
                <label>التاريخ</label>
                <input type="date" name="purchase_date">
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                حفظ الاعتماد
            </button>
        </div>

    </form>

</div>

<script>
function toggleFields() {
    let type = document.getElementById('type').value;

    document.querySelectorAll('.purchase-field').forEach(el => {
        el.style.display = (type === 'purchase') ? 'block' : 'none';
    });

    document.querySelectorAll('.repair-field').forEach(el => {
        el.style.display = (type === 'repair') ? 'block' : 'none';
    });
}
</script>

@endsection