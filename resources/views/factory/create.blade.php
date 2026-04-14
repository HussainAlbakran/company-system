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

    <div class="card mb-4">
        <div class="card-body">

            <form action="{{ route('production-orders.store') }}" method="POST">
                @csrf

                {{-- اختيار المشروع --}}
                <div class="mb-3">
                    <label class="form-label">المشروع</label>
                    <select name="project_id" id="projectSelect" class="form-control" required>
                        <option value="">-- اختر المشروع --</option>
                        @foreach($projects as $project)
                            <option 
                                value="{{ $project->id }}"
                                data-measurements='@json($project->architectMeasurements)'
                                {{ old('project_id') == $project->id ? 'selected' : '' }}
                            >
                                {{ $project->project_code }} - {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 🔥 عرض المقاسات --}}
                <div id="measurementsBox" class="mb-4" style="display:none;">
                    <h5>📐 مقاسات المشروع</h5>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>النوع</th>
                                    <th>العنصر</th>
                                    <th>الطول</th>
                                    <th>العرض</th>
                                    <th>الارتفاع</th>
                                    <th>العدد</th>
                                    <th>المساحة</th>
                                    <th>الحجم</th>
                                </tr>
                            </thead>
                            <tbody id="measurementsTable">
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- بيانات أمر الإنتاج --}}
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

{{-- 🔥 سكربت عرض المقاسات --}}
<script>
document.getElementById('projectSelect').addEventListener('change', function () {

    let selected = this.options[this.selectedIndex];
    let measurements = selected.getAttribute('data-measurements');

    let box = document.getElementById('measurementsBox');
    let table = document.getElementById('measurementsTable');

    table.innerHTML = '';

    if (!measurements) {
        box.style.display = 'none';
        return;
    }

    let data = JSON.parse(measurements);

    if (data.length === 0) {
        box.style.display = 'none';
        return;
    }

    data.forEach(item => {
        table.innerHTML += `
            <tr>
                <td>${item.type ?? '-'}</td>
                <td>${item.name}</td>
                <td>${item.length ?? '-'}</td>
                <td>${item.width ?? '-'}</td>
                <td>${item.height ?? '-'}</td>
                <td>${item.quantity}</td>
                <td>${item.area}</td>
                <td>${item.volume}</td>
            </tr>
        `;
    });

    box.style.display = 'block';
});
</script>

@endsection