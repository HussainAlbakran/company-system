@extends('layouts.app')

@section('page_title', 'تعيينات المشاريع')
@section('page_subtitle', 'مسؤول المشروع وحساب العميل في البوابة')

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div>
            <h1 class="page-title">تعيينات المشاريع</h1>
            <p class="page-subtitle">للتعديل التفصيلي استخدم مشاريع الهندسة أو لوحة الإدارة المتقدمة</p>
        </div>
        <a href="{{ route('administration.index') }}" class="btn btn-secondary btn-sm">مركز الإدارة</a>
    </div>

    <div class="table-wrap" style="margin-top:12px;">
        <table>
            <thead>
                <tr>
                    <th>المشروع</th>
                    <th>نسبة الإنجاز</th>
                    <th>الحالة</th>
                    <th>الموظف المسؤول</th>
                    <th>عميل البوابة</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                    <tr>
                        <td>{{ $project->name }}</td>
                        <td>{{ (int) round($project->progress_percentage ?? 0) }}٪</td>
                        <td>{{ $project->status }}</td>
                        <td>{{ $project->responsibleEmployee->name ?? '—' }}</td>
                        <td>
                            @if($project->clientUser)
                                {{ $project->clientUser->name }}<br><span style="color:#92a6c4;font-size:10px;">{{ $project->clientUser->email }}</span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="actions-row">
                            <a href="{{ route('engineering-projects.edit', $project->id) }}" class="btn btn-primary btn-sm">تعديل</a>
                            <a href="{{ url('/admin/projects/'.$project->id.'/edit') }}" class="btn btn-secondary btn-sm" target="_blank" rel="noopener">لوحة الإدارة</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-row">لا توجد مشاريع</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:12px;">{{ $projects->links() }}</div>
</div>
@endsection
