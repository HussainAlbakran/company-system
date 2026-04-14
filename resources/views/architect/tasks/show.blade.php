@extends('layouts.app')

@section('page_title', 'Design Details')
@section('page_subtitle', 'Architect measurements and planning')

@section('content')

<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h2>المعماري - تفاصيل المشروع</h2>
            <p>الرسم، التخطيط، المقاسات، واعتماد المرحلة</p>
        </div>

        <div class="actions-row">
            <a href="{{ route('architect-tasks.index') }}" class="btn btn-secondary">
                رجوع
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-danger">
            {{ session('error') }}
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

    {{-- بيانات المشروع --}}
    <div class="page-card" style="margin-bottom:24px;">
        <div class="page-header">
            <h2 style="margin:0; font-size:22px;">بيانات المشروع</h2>
        </div>

        <div class="details-grid">
            <div class="detail-box">
                <strong>رقم المشروع</strong>
                <div>{{ $project->project_code }}</div>
            </div>

            <div class="detail-box">
                <strong>اسم المشروع</strong>
                <div>{{ $project->name }}</div>
            </div>

            <div class="detail-box">
                <strong>العميل</strong>
                <div>{{ $project->client_name }}</div>
            </div>

            <div class="detail-box">
                <strong>المقاول الرئيسي</strong>
                <div>{{ $project->main_contractor ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>المرحلة الحالية</strong>
                <div>
                    <span class="badge badge-blue">{{ $project->current_stage }}</span>
                </div>
            </div>

            <div class="detail-box">
                <strong>الحالة</strong>
                <div>{{ $project->status }}</div>
            </div>

            <div class="detail-box detail-box-full">
                <strong>وصف المشروع</strong>
                <div>{{ $project->description ?? '-' }}</div>
            </div>
        </div>
    </div>

    {{-- بيانات المعماري --}}
    <div class="page-card" style="margin-bottom:24px;">
        <div class="page-header">
            <h2 style="margin:0; font-size:22px;">بيانات الرسم والتخطيط</h2>
        </div>

        <form action="{{ route('architect-tasks.update', $project->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-grid">

                <div class="form-group">
                    <label>نوع الرسم</label>
                    <input type="text" name="drawing_type" value="{{ old('drawing_type', $architectTask->drawing_type) }}">
                </div>

                <div class="form-group">
                    <label>حالة الرسم</label>
                    <select name="drawing_status" required>
                        <option value="pending" {{ old('drawing_status', $architectTask->drawing_status) == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="in_progress" {{ old('drawing_status', $architectTask->drawing_status) == 'in_progress' ? 'selected' : '' }}>قيد العمل</option>
                        <option value="completed" {{ old('drawing_status', $architectTask->drawing_status) == 'completed' ? 'selected' : '' }}>مكتمل</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>حالة التخطيط</label>
                    <select name="planning_status" required>
                        <option value="pending" {{ old('planning_status', $architectTask->planning_status) == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="in_progress" {{ old('planning_status', $architectTask->planning_status) == 'in_progress' ? 'selected' : '' }}>قيد العمل</option>
                        <option value="completed" {{ old('planning_status', $architectTask->planning_status) == 'completed' ? 'selected' : '' }}>مكتمل</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>ملف الرسم</label>
                    <input type="file" name="drawing_file">
                    @if($architectTask->drawing_file)
                        <div style="margin-top:8px;">
                            <a href="{{ asset('storage/' . $architectTask->drawing_file) }}" target="_blank" class="btn btn-sm btn-primary">
                                فتح الملف الحالي
                            </a>
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label>ملف التخطيط</label>
                    <input type="file" name="planning_file">
                    @if($architectTask->planning_file)
                        <div style="margin-top:8px;">
                            <a href="{{ asset('storage/' . $architectTask->planning_file) }}" target="_blank" class="btn btn-sm btn-primary">
                                فتح الملف الحالي
                            </a>
                        </div>
                    @endif
                </div>

                <div class="form-group form-group-full">
                    <label>ملاحظات المعماري</label>
                    <textarea name="notes">{{ old('notes', $architectTask->notes) }}</textarea>
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">حفظ بيانات المعماري</button>
            </div>
        </form>
    </div>

    {{-- إدخال المقاسات في جدول كبير --}}
    <div class="page-card" style="margin-bottom:24px;">
        <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
            <div>
                <h2 style="margin:0; font-size:22px;">جدول إدخال المقاسات</h2>
                <p style="margin:8px 0 0; color:#6b7280;">
                    أضف صفوفًا كثيرة كما تريد. السعر سيكون للمصنع والإدارة فقط.
                </p>
            </div>

            <button type="button" class="btn btn-success" onclick="addMeasurementRow()">
                + إضافة صف جديد
            </button>
        </div>

        <form action="{{ route('architect.measurements.store', $project->id) }}" method="POST" id="bulkMeasurementForm">
            @csrf

            <div class="table-wrap">
                <table id="measurement-entry-table">
                    <thead>
                        <tr>
                            <th>النوع</th>
                            <th>اسم العنصر</th>
                            <th>الطول</th>
                            <th>العرض</th>
                            <th>الارتفاع</th>
                            <th>العدد</th>
                            <th>الوحدة</th>
                            <th>السعر</th>
                            <th>ملاحظات</th>
                            <th>حذف الصف</th>
                        </tr>
                    </thead>
                    <tbody id="measurement-entry-body">
                        <tr>
                            <td>
                                <input type="text" name="rows[0][type]" placeholder="جدار / غرفة / باب">
                            </td>
                            <td>
                                <input type="text" name="rows[0][name]" placeholder="اسم العنصر" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="rows[0][length]" placeholder="0.00">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="rows[0][width]" placeholder="0.00">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="rows[0][height]" placeholder="0.00">
                            </td>
                            <td>
                                <input type="number" name="rows[0][quantity]" value="1" min="1" required>
                            </td>
                            <td>
                                <select name="rows[0][unit]">
                                    <option value="m">متر</option>
                                    <option value="cm">سم</option>
                                    <option value="mm">ملم</option>
                                </select>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="rows[0][price]" placeholder="0.00">
                            </td>
                            <td>
                                <textarea name="rows[0][notes]" rows="1" placeholder="ملاحظات"></textarea>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeMeasurementRow(this)">
                                    حذف
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="form-actions" style="margin-top:16px;">
                <button type="submit" class="btn btn-primary">
                    حفظ جميع الصفوف
                </button>
            </div>
        </form>
    </div>

    {{-- جدول المقاسات --}}
    <div class="page-card" style="margin-bottom:24px;">
        <div class="page-header">
            <h2 style="margin:0; font-size:22px;">جدول المقاسات المحفوظة</h2>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>النوع</th>
                        <th>العنصر</th>
                        <th>الطول</th>
                        <th>العرض</th>
                        <th>الارتفاع</th>
                        <th>العدد</th>
                        <th>الوحدة</th>
                        <th>المساحة</th>
                        <th>الحجم</th>
                        <th>السعر</th>
                        <th>ملاحظات</th>
                        <th>حذف</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($measurements as $measurement)
                        <tr>
                            <td>{{ $measurement->id }}</td>
                            <td>{{ $measurement->type ?? '-' }}</td>
                            <td>{{ $measurement->name }}</td>
                            <td>{{ $measurement->length }}</td>
                            <td>{{ $measurement->width }}</td>
                            <td>{{ $measurement->height }}</td>
                            <td>{{ $measurement->quantity }}</td>
                            <td>{{ $measurement->unit ?? 'm' }}</td>
                            <td>{{ $measurement->area }}</td>
                            <td>{{ $measurement->volume }}</td>
                            <td>{{ $measurement->price ?? '-' }}</td>
                            <td>{{ $measurement->notes ?? '-' }}</td>
                            <td>
                                <form action="{{ route('architect.measurements.destroy', $measurement->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف المقاس؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="empty-row">
                                لا توجد مقاسات حتى الآن
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- اعتماد المعماري --}}
    <div class="page-card">
        <div class="page-header">
            <h2 style="margin:0; font-size:22px;">اعتماد المرحلة</h2>
            <p style="margin:8px 0 0; color:#6b7280;">
                بعد التأكد من اكتمال الرسم والتخطيط والمقاسات، يمكن اعتماد المشروع وتحويله للمرحلة التالية.
            </p>
        </div>

        <div class="form-actions" style="display:flex; gap:10px; flex-wrap:wrap;">
            <form action="{{ route('architect-tasks.approve', $project->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success">
                    اعتماد المعماري وتحويل المشروع
                </button>
            </form>

            <form action="{{ route('architect-tasks.sendToFactory', $project->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">
                    إرسال المقاسات للمصنع
                </button>
            </form>
        </div>
    </div>

</div>

<script>
let measurementRowIndex = 1;

function addMeasurementRow() {
    const tbody = document.getElementById('measurement-entry-body');
    const row = document.createElement('tr');

    row.innerHTML = `
        <td>
            <input type="text" name="rows[${measurementRowIndex}][type]" placeholder="جدار / غرفة / باب">
        </td>
        <td>
            <input type="text" name="rows[${measurementRowIndex}][name]" placeholder="اسم العنصر" required>
        </td>
        <td>
            <input type="number" step="0.01" name="rows[${measurementRowIndex}][length]" placeholder="0.00">
        </td>
        <td>
            <input type="number" step="0.01" name="rows[${measurementRowIndex}][width]" placeholder="0.00">
        </td>
        <td>
            <input type="number" step="0.01" name="rows[${measurementRowIndex}][height]" placeholder="0.00">
        </td>
        <td>
            <input type="number" name="rows[${measurementRowIndex}][quantity]" value="1" min="1" required>
        </td>
        <td>
            <select name="rows[${measurementRowIndex}][unit]">
                <option value="m">متر</option>
                <option value="cm">سم</option>
                <option value="mm">ملم</option>
            </select>
        </td>
        <td>
            <input type="number" step="0.01" name="rows[${measurementRowIndex}][price]" placeholder="0.00">
        </td>
        <td>
            <textarea name="rows[${measurementRowIndex}][notes]" rows="1" placeholder="ملاحظات"></textarea>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeMeasurementRow(this)">
                حذف
            </button>
        </td>
    `;

    tbody.appendChild(row);
    measurementRowIndex++;
}

function removeMeasurementRow(button) {
    const tbody = document.getElementById('measurement-entry-body');
    if (tbody.rows.length > 1) {
        button.closest('tr').remove();
    }
}
</script>

@endsection