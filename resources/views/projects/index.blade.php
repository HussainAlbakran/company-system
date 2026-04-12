@extends('layouts.app')

@section('content')
<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 class="page-title"> المشاريع الهندسية</h1>
            <p>إدارة جميع المشاريع</p>
        </div>

        <a href="{{ route('engineering-projects.create') }}" class="btn btn-primary">
            ➕ إضافة مشروع
        </a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>المشروع</th>
                    <th>القسم</th>
                    <th>المسؤول</th>
                    <th>الحالة</th>
                    <th>نسبة الإنجاز</th>
                    <th>القيمة</th>
                    <th>المصاريف</th>
                    <th>إجراءات</th>
                </tr>
            </thead>

            <tbody>
                @forelse($projects as $project)
                <tr>

                    <td>
                        <a href="{{ route('engineering-projects.show', $project->id) }}">
                            {{ $project->name }}
                        </a>
                    </td>

                    <td>{{ $project->department->name ?? '-' }}</td>

                    <td>{{ $project->responsibleEmployee->name ?? '-' }}</td>

                    <td>{{ $project->status ?? '-' }}</td>

                    <td>
                        <span class="badge badge-blue">
                            {{ $project->progress_percentage }}%
                        </span>
                    </td>

                    <td>{{ number_format($project->project_value, 2) }}</td>

                    <td>{{ number_format($project->expenses, 2) }}</td>

                    <td style="display:flex; gap:6px;">
                        <a href="{{ route('engineering-projects.show', $project->id) }}" class="btn btn-sm btn-blue">عرض</a>

                        <a href="{{ route('engineering-projects.edit', $project->id) }}" class="btn btn-sm btn-orange">تعديل</a>

                        <form action="{{ route('engineering-projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-red">حذف</button>
                        </form>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-row">
                        لا توجد مشاريع
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($projects, 'links'))
        <div style="margin-top:16px;">
            {{ $projects->links() }}
        </div>
    @endif

</div>
@endsection