@extends('layouts.app')

@section('content')
<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">ملف الموظف</h1>
            <p style="margin:8px 0 0; color:#6b7280;">عرض كامل لبيانات الموظف والمرفقات الخاصة به</p>
        </div>

        <div class="actions">
            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">تعديل</a>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">رجوع</a>
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

    {{-- البيانات الأساسية --}}
    <div class="page-card" style="margin-bottom:24px;">
        <div class="page-header">
            <h2 style="margin:0; font-size:24px;">البيانات الأساسية</h2>
        </div>

        <div class="details-grid">
            <div class="detail-box">
                <strong>الاسم</strong>
                <div>{{ $employee->name ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>الرقم الوظيفي</strong>
                <div>{{ $employee->employee_number ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>المسمى الوظيفي</strong>
                <div>{{ $employee->job_title ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>الجوال</strong>
                <div>{{ $employee->phone ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>البريد الإلكتروني</strong>
                <div>{{ $employee->email ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>القسم</strong>
                <div>{{ $employee->department->name ?? '-' }}</div>
            </div>
        </div>
    </div>

    {{-- العهدة --}}
    <div class="page-card" style="margin-bottom:24px;">
        <div class="page-header">
            <h2>العهدة</h2>
        </div>

        <form action="{{ route('employees.assets.store', $employee->id) }}" method="POST">
            @csrf

            <div class="form-grid">

                <div class="form-group">
                    <label>اسم الأصل</label>
                    <input type="text" name="asset_name" required placeholder="مثال: سيارة / لابتوب">
                </div>

                <div class="form-group">
                    <label>تاريخ البداية</label>
                    <input type="date" name="start_date" required>
                </div>

                <div class="form-group">
                    <label>تاريخ النهاية</label>
                    <input type="date" name="end_date">
                </div>

                <div class="form-group">
                    <label>الحالة</label>
                    <select name="status">
                        <option value="active">نشط</option>
                        <option value="ended">منتهي</option>
                        <option value="lost">مفقود</option>
                        <option value="damaged">تالف</option>
                    </select>
                </div>

                <div class="form-group form-group-full">
                    <label>ملاحظات</label>
                    <textarea name="notes"></textarea>
                </div>

            </div>

            <button class="btn btn-primary">إضافة عهدة</button>
        </form>
    </div>

    {{-- جدول العهد --}}
    <div class="page-card">
        <div class="page-header">
            <h2>سجل العهدة</h2>
        </div>

        <table>
            <thead>
                <tr>
                    <th>الأصل</th>
                    <th>الرقم التسلسلي</th>
                    <th>تاريخ البداية</th>
                    <th>الحالة</th>
                    <th>إجراء</th>
                </tr>
            </thead>

            <tbody>
                @foreach($employee->assets as $asset)
                <tr>
                    <td>{{ $asset->asset_name }}</td>
                    <td>
                        <strong>{{ $asset->serial_number }}</strong>
                    </td>
                    <td>{{ $asset->start_date }}</td>
                    <td>{{ $asset->status }}</td>
                    <td>
                        <form action="{{ route('employees.assets.destroy', $asset->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">حذف</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection