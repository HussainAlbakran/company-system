@extends('layouts.app')

@section('content')
<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">الموظفون</h1>
            <p style="margin:8px 0 0; color:#6b7280;">إدارة الموظفين وملفاتهم وبياناتهم الأساسية</p>
        </div>

        <a href="{{ route('employees.create') }}" class="btn btn-success">
            ➕ إضافة موظف
        </a>
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

    <div class="page-card" style="margin-bottom:24px;">
        <form method="GET" action="{{ route('employees.index') }}">
            <div class="form-grid" style="align-items:end;">
                <div class="form-group">
                    <label>بحث عن موظف</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="الاسم / الرقم الوظيفي / الجوال / البريد"
                    >
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">بحث</button>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary">إعادة ضبط</a>
                </div>
            </div>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>الرقم الوظيفي</th>
                    <th>المسمى الوظيفي</th>
                    <th>الجوال</th>
                    <th>البريد</th>
                    <th>القسم</th>
                    <th>انتهاء الإقامة</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>

            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td>
                            <a href="{{ route('employees.show', $employee) }}" class="employee-link">
                                {{ $employee->name }}
                            </a>
                        </td>

                        <td>{{ $employee->employee_number ?? '-' }}</td>
                        <td>{{ $employee->job_title ?? '-' }}</td>
                        <td>{{ $employee->phone ?? '-' }}</td>
                        <td>{{ $employee->email ?? '-' }}</td>
                        <td>{{ $employee->department->name ?? '-' }}</td>
                        <td>{{ $employee->residency_expiry_date ?? '-' }}</td>
                        <td>
                            @if(($employee->status ?? '') === 'active')
                                <span class="badge badge-green">نشط</span>
                            @elseif(($employee->status ?? '') === 'inactive')
                                <span class="badge badge-red">غير نشط</span>
                            @else
                                <span class="badge badge-gray">{{ $employee->status ?? '-' }}</span>
                            @endif
                        </td>

                        <td>
                            <div class="actions-row">
                                <a href="{{ route('employees.show', $employee) }}" class="btn btn-success btn-sm">
                                    ملف الموظف
                                </a>

                                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning btn-sm">
                                    تعديل
                                </a>

                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف الموظف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty-row">لا يوجد موظفون</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($employees, 'links'))
        <div style="margin-top:20px;">
            {{ $employees->links() }}
        </div>
    @endif

</div>
@endsection