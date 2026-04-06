@extends('layouts.app')

@section('content')

<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 class="page-title">الأقسام</h1>
            <p style="color:#6b7280; margin-top:8px;">
                إدارة جميع أقسام الشركة
            </p>
        </div>

        <a href="{{ route('departments.create') }}" class="btn btn-primary">
            + إضافة قسم
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-wrap">

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>اسم القسم</th>
                    <th style="width:180px;">الإجراءات</th>
                </tr>
            </thead>

            <tbody>
                @forelse($departments as $department)
                    <tr>
                        <td>{{ $department->id }}</td>

                        <td>
                            <strong>{{ $department->name }}</strong>
                        </td>

                        <td>
                            <div style="display:flex; gap:8px;">

                                <a href="{{ route('departments.edit', $department->id) }}"
                                   class="btn btn-warning btn-sm">
                                    تعديل
                                </a>

                                <form action="{{ route('departments.destroy', $department->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا القسم؟')">
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
                        <td colspan="3" class="empty-row">
                            لا توجد أقسام حالياً
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

</div>

@endsection