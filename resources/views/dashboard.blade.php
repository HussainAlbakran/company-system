@extends('layouts.app')

@section('content')
<div class="page-card">

    <!-- HEADER -->
    <div class="page-header">
        <h1 class="page-title">Dashboard</h1>
        <p>مرحبًا بك في نظام شركة التقدم للخرسانة الجاهزة</p>
    </div>

    {{-- 🔔 تنبيهات الإقامات --}}
    @if(auth()->user()->isAdmin() || auth()->user()->isHR())

        @if($expiredResidencyEmployees->count() > 0)
            <div style="background:#dc2626; color:white; padding:12px; border-radius:8px; margin-bottom:10px;">
                🚨 يوجد {{ $expiredResidencyEmployees->count() }} موظف انتهت إقامتهم
            </div>
        @endif

        @if($residencyExpiringEmployees->count() > 0)
            <div style="background:#f59e0b; color:white; padding:12px; border-radius:8px; margin-bottom:15px;">
                ⏳ يوجد {{ $residencyExpiringEmployees->count() }} موظف إقامتهم تنتهي قريبًا
            </div>
        @endif

    @endif

    {{-- 🔥 تنبيهات المشاريع --}}
    @if(auth()->user()->isAdmin())

        @if($delayedProjectsCount > 0 || $endingSoonProjectsCount > 0)
            <div style="margin-bottom:20px; display:flex; gap:10px; flex-wrap:wrap;">

                @if($delayedProjectsCount > 0)
                    <div style="background:#dc2626; color:white; padding:12px 20px; border-radius:10px;">
                        🔴 مشاريع متأخرة: {{ $delayedProjectsCount }}
                    </div>
                @endif

                @if($endingSoonProjectsCount > 0)
                    <div style="background:#f59e0b; color:white; padding:12px 20px; border-radius:10px;">
                        ⏳ مشاريع تنتهي قريب: {{ $endingSoonProjectsCount }}
                    </div>
                @endif

            </div>
        @endif

    @endif

    <!-- USER INFO -->
    <div class="page-card" style="margin-bottom: 24px;">
        <h2>👋 أهلاً {{ auth()->user()->name }}</h2>
    </div>

    {{--  إحصائيات --}}
    @if(auth()->user()->isAdmin())
    <div class="stats-grid" style="margin-bottom: 24px;">
        <div class="stat-card">
            💰 الرواتب: {{ number_format($monthlySalaryBudget, 2) }}
        </div>
        <div class="stat-card">
             المشاريع: {{ number_format($currentProjectsValue, 2) }}
        </div>
        <div class="stat-card">
            💸 المصاريف: {{ number_format($currentProjectsExpenses, 2) }}
        </div>
    </div>
    @endif

    {{-- 🔥 جدول المشاريع الكامل --}}
    @if(auth()->user()->isAdmin())

    <div class="page-card">
        <div class="page-header">
            <h2>قائمة المشاريع</h2>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>اسم المشروع</th>
                        <th>القسم</th>
                        <th>المسؤول</th>
                        <th>تاريخ النهاية</th>
                        <th>المدة</th>
                        <th>الملاحظات</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($adminProjects as $project)
                    <tr>

                        <td>
                            <a href="{{ route('engineering.projects.show', $project->id) }}">
                                {{ $project->name }}
                            </a>
                        </td>

                        <td>{{ $project->department->name ?? '-' }}</td>

                        <td>{{ $project->responsibleEmployee->name ?? '-' }}</td>

                        <td>{{ $project->end_date ?? '-' }}</td>

                        <td>
                            @if($project->is_delayed)
                                <span style="color:red;">
                                    متأخر {{ abs($project->days_remaining) }} يوم
                                </span>
                            @elseif($project->is_ending_soon)
                                <span style="color:orange;">
                                    {{ $project->days_remaining }} يوم
                                </span>
                            @else
                                <span style="color:green;">
                                    {{ $project->days_remaining ?? '-' }} يوم
                                </span>
                            @endif
                        </td>

                        <td>
                            {{ $project->notes ?? '-' }}
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6">لا توجد مشاريع</td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>
    </div>

    @endif

</div>
@endsection