@extends('layouts.app')

@section('page_title', 'Installation')
@section('page_subtitle', 'Read-only execution from production orders')

@section('content')

<x-ui.card title="Installation Projects" subtitle="Required / Produced / Remaining from ProductionOrder only">
    <x-ui.table>
            <thead>
                <tr>
                    <th>رقم المشروع</th>
                    <th>اسم المشروع</th>
                    <th>العميل</th>
                    <th>ملف الرسم</th>
                    <th>ملف التخطيط</th>
                    <th>عدد المقاسات</th>
                    <th>المطلوب</th>
                    <th>تم إنتاجه</th>
                    <th>المتبقي</th>
                    <th>نسبة الإنجاز</th>
                    <th>الحالة</th>
                    <th>فتح المشروع</th>
                </tr>
            </thead>

            <tbody>
                @forelse($projects as $project)
                    <tr>
                        <td>{{ $project->project_code }}</td>
                        <td><strong>{{ $project->name }}</strong></td>
                        <td>{{ $project->client_name }}</td>

                        <td>
                            @if($project->architectTask && $project->architectTask->drawing_file)
                                <span class="badge badge-green">موجود</span>
                            @else
                                <span class="badge badge-gray">غير مرفوع</span>
                            @endif
                        </td>

                        <td>
                            @if($project->architectTask && $project->architectTask->planning_file)
                                <span class="badge badge-green">موجود</span>
                            @else
                                <span class="badge badge-gray">غير مرفوع</span>
                            @endif
                        </td>

                        <td>
                            <span class="badge badge-blue">
                                {{ $project->measurements_count ?? 0 }}
                            </span>
                        </td>

                        <td>{{ number_format($project->planned_quantity ?? 0, 2) }}</td>

                        <td>
                            <span class="badge badge-green">
                                {{ number_format($project->produced_quantity ?? 0, 2) }}
                            </span>
                        </td>

                        <td>
                            @if(($project->remaining_quantity ?? 0) > 0)
                                <span class="badge badge-orange">
                                    {{ number_format($project->remaining_quantity, 2) }}
                                </span>
                            @else
                                <span class="badge badge-green">مكتمل</span>
                            @endif
                        </td>

                        <td>
                            <x-ui.progress :value="$project->progress_percentage ?? 0" />
                        </td>

                        <td>
                            <span class="badge badge-blue">{{ $project->current_stage }}</span>
                        </td>

                        <td>
                            <a href="{{ route('installations.show', $project->id) }}" class="btn btn-primary btn-sm">فتح المشروع</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="empty-row">
                            لا توجد مشاريع حالياً
                        </td>
                    </tr>
                @endforelse
            </tbody>
    </x-ui.table>
</x-ui.card>

@endsection