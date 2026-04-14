@extends('layouts.app')

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 class="page-title">لوحة مدير المصنع</h1>
            <p style="margin:8px 0 0; color:#6b7280;">بيانات المصنع والموظفين المرتبطين به</p>
        </div>
        <a href="{{ route('factory.index') }}" class="btn btn-secondary">قسم المصنع</a>
    </div>

    <div class="form-grid">
        <div class="detail-box"><strong>اسم المصنع</strong><br>{{ $factory->name ?? '-' }}</div>
        <div class="detail-box"><strong>الموقع</strong><br>{{ $factory->location ?? '-' }}</div>
        <div class="detail-box"><strong>عدد الموظفين</strong><br>{{ $employeesCount ?? 0 }}</div>
    </div>

    <div class="table-wrap" style="margin-top:20px;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>الرقم الوظيفي</th>
                    <th>الوظيفة</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td>{{ $employee->id }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->employee_number ?? '-' }}</td>
                        <td>{{ $employee->job_title ?? '-' }}</td>
                        <td>{{ $employee->status ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-row">لا يوجد موظفون مرتبطون بهذا المصنع</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
