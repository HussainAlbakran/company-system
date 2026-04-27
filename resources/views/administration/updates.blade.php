@extends('layouts.app')

@section('page_title', 'تحديثات التقدم')
@section('page_subtitle', 'سجل تحديثات التقدم من الموظفين والفرق')

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div>
            <h1 class="page-title">مراجعة تحديثات التقدم</h1>
            <p class="page-subtitle">عرض شامل — الحذف/التعديل المتقدم من مسار الهندسة عند الحاجة</p>
        </div>
        <a href="{{ route('administration.index') }}" class="btn btn-secondary btn-sm">مركز الإدارة</a>
    </div>

    <div class="table-wrap" style="margin-top:12px;">
        <table>
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>المشروع</th>
                    <th>العنوان</th>
                    <th>الوصف</th>
                    <th>التقدم</th>
                    <th>المسجل</th>
                </tr>
            </thead>
            <tbody>
                @forelse($updates as $u)
                    <tr>
                        <td>{{ $u->created_at?->format('Y-m-d H:i') }}</td>
                        <td>{{ $u->project->name ?? '—' }}</td>
                        <td>{{ $u->title }}</td>
                        <td style="max-width:220px; white-space:pre-wrap; font-size:11px;">{{ \Illuminate\Support\Str::limit($u->description ?? '', 120) }}</td>
                        <td>{{ (int) $u->progress }}٪</td>
                        <td>{{ $u->creator?->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-row">لا توجد تحديثات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:12px;">{{ $updates->links() }}</div>
</div>
@endsection
