@extends('layouts.app')

@section('page_title', 'مركز الإدارة')
@section('page_subtitle', 'إدارة التعيينات والمتابعة')

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 class="page-title">مركز الإدارة</h1>
            <p class="page-subtitle">لوحة موحّدة للمشرف — مرتبطة بالصفحات الحالية دون تكرار غير ضروري</p>
        </div>
        <div class="actions-row">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">لوحة التحكم</a>
        </div>
    </div>

    <div class="stats-grid" style="margin-top:12px;">
        <div class="stat-card">
            <div class="stat-label">إجمالي المشاريع</div>
            <div class="stat-value">{{ $totalProjects }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">مستخدمو البوابة الخارجية</div>
            <div class="stat-value">{{ $totalClientUsers }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">الموظفون (سجل HR)</div>
            <div class="stat-value">{{ $totalEmployees }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">إقامات تنتهي خلال 30 يوم</div>
            <div class="stat-value">{{ $upcomingResidencyCount }}</div>
        </div>
    </div>

    <div class="page-header" style="margin-top:16px;">
        <h2 class="page-title" style="font-size:15px;">ملخص حالات المشاريع</h2>
    </div>
    <div class="details-grid">
        @forelse($statusSummary as $status => $count)
            <div class="detail-box">
                <div class="stat-label">{{ $status }}</div>
                <div class="stat-value" style="font-size:22px;">{{ $count }}</div>
            </div>
        @empty
            <div class="detail-box"><span class="empty-row">لا توجد مشاريع</span></div>
        @endforelse
    </div>

    <div class="page-header" style="margin-top:20px;">
        <h2 class="page-title" style="font-size:15px;">اختصارات الإدارة</h2>
    </div>
    <div class="actions-row" style="margin-top:8px; flex-wrap:wrap;">
        <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">الموظفون (HR)</a>
        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">المستخدمون</a>
        <a href="{{ route('administration.assignments') }}" class="btn btn-secondary btn-sm">تعيينات المشاريع</a>
        <a href="{{ route('engineering-projects.index') }}" class="btn btn-secondary btn-sm">مشاريع الهندسة</a>
        <a href="{{ route('administration.updates') }}" class="btn btn-secondary btn-sm">تحديثات التقدم</a>
    </div>

    <div class="page-header" style="margin-top:20px;">
        <h2 class="page-title" style="font-size:15px;">آخر التحديثات</h2>
    </div>
    <div class="table-wrap" style="margin-top:8px;">
        <table>
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>المشروع</th>
                    <th>العنوان</th>
                    <th>التقدم</th>
                    <th>المسجل</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentUpdates as $u)
                    <tr>
                        <td>{{ $u->created_at?->format('Y-m-d H:i') }}</td>
                        <td>{{ $u->project->name ?? '—' }}</td>
                        <td>{{ $u->title }}</td>
                        <td>{{ (int) $u->progress }}٪</td>
                        <td>{{ $u->creator?->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-row">لا توجد تحديثات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="page-header" style="margin-top:20px;">
        <h2 class="page-title" style="font-size:15px;">إشعارات النظام الداخلية</h2>
    </div>
    <div class="table-wrap" style="margin-top:8px;">
        <table>
            <thead>
                <tr>
                    <th>العنوان</th>
                    <th>الرسالة</th>
                    <th>التاريخ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($adminInternalNotifications as $notification)
                    <tr>
                        <td>{{ $notification->title }}</td>
                        <td>{{ $notification->message ?? '—' }}</td>
                        <td>{{ $notification->created_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="empty-row">لا توجد إشعارات داخلية</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
