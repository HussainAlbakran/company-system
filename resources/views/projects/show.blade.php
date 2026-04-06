@extends('layouts.app')

@section('content')
<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">تفاصيل المشروع</h1>
            <p style="margin:8px 0 0; color:#6b7280;">عرض كامل لبيانات المشروع والتحديثات الخاصة به</p>
        </div>

        <div class="actions" style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('engineering.projects.edit', $project->id) }}" class="btn btn-warning">تعديل</a>
            <a href="{{ route('engineering.projects.index') }}" class="btn btn-secondary">رجوع</a>
        </div>
    </div>

    <div class="details-grid" style="margin-bottom: 24px;">
        <div class="detail-box">
            <strong>اسم المشروع</strong>
            <div>{{ $project->name }}</div>
        </div>

        <div class="detail-box">
            <strong>القسم</strong>
            <div>{{ $project->department->name ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>الموظف المسؤول</strong>
            <div>{{ $project->responsibleEmployee->name ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>الحالة</strong>
            <div>{{ $project->status ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>نسبة الإنجاز</strong>
            <div>{{ $project->progress_percentage ?? 0 }}%</div>
        </div>

        <div class="detail-box">
            <strong>قيمة المشروع</strong>
            <div>{{ number_format($project->project_value ?? 0, 2) }}</div>
        </div>

        <div class="detail-box">
            <strong>المصاريف</strong>
            <div>{{ number_format($project->expenses ?? 0, 2) }}</div>
        </div>

        <div class="detail-box">
            <strong>تاريخ البداية</strong>
            <div>{{ $project->start_date ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>تاريخ النهاية</strong>
            <div>{{ $project->end_date ?? '-' }}</div>
        </div>

        <div class="detail-box detail-box-full">
            <strong>الوصف</strong>
            <div>{{ $project->description ?? '-' }}</div>
        </div>

        <div class="detail-box detail-box-full">
            <strong>الملاحظات</strong>
            <div>{{ $project->notes ?? '-' }}</div>
        </div>

        <div class="detail-box detail-box-full">
            <strong>ملف المشروع PDF</strong>
            <div style="margin-top:10px;">
                @if($project->project_pdf)
                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        <a href="{{ asset('storage/' . $project->project_pdf) }}" target="_blank" class="btn btn-success">
                            فتح الملف
                        </a>
                        <a href="{{ asset('storage/' . $project->project_pdf) }}" download class="btn btn-primary">
                            تحميل الملف
                        </a>
                    </div>
                @else
                    <span style="color:#6b7280;">لا يوجد ملف مرفق</span>
                @endif
            </div>
        </div>
    </div>

    <div class="page-card" style="margin-bottom: 24px;">
        <div class="page-header">
            <h2 style="margin:0; font-size:24px;">إضافة تحديث جديد</h2>
            <p style="margin-top:8px; color:#6b7280;">أضف تحديثًا جديدًا للمشروع مع نسبة الإنجاز والمرفقات</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom:15px;">
                <ul style="margin:0; padding-right:20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('engineering.projects.updates.store', $project->id) }}" method="POST" enctype="multipart/form-data" class="form-card">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label>عنوان التحديث</label>
                    <input type="text" name="title" value="{{ old('title') }}" required>
                </div>

                <div class="form-group">
                    <label>نسبة الإنجاز</label>
                    <input type="number" name="progress" min="0" max="100" value="{{ old('progress', $project->progress_percentage ?? 0) }}" required>
                </div>

                <div class="form-group form-group-full">
                    <label>وصف التحديث</label>
                    <textarea name="description" rows="4">{{ old('description') }}</textarea>
                </div>

                <div class="form-group form-group-full">
                    <label>مرفق</label>
                    <input type="file" name="attachment">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">حفظ التحديث</button>
            </div>
        </form>
    </div>

    <div class="page-card">
        <div class="page-header">
            <h2 style="margin:0; font-size:24px;">تحديثات المشروع</h2>
            <p style="margin-top:8px; color:#6b7280;">سجل كامل لكل ما تم على المشروع</p>
        </div>

        @if($project->updates->count())
            <div class="timeline">
                @foreach($project->updates->sortByDesc('created_at') as $update)
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>

                        <div class="timeline-content">
                            <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; align-items:flex-start;">
                                <div>
                                    <h3 style="margin:0 0 8px; font-size:20px; font-weight:800;">
                                        {{ $update->title }}
                                    </h3>
                                    <div style="color:#6b7280; font-size:14px;">
                                        {{ $update->created_at->format('Y-m-d H:i') }}
                                    </div>
                                </div>

                                <div style="background:#eff6ff; border:1px solid #bfdbfe; color:#1d4ed8; padding:8px 12px; border-radius:999px; font-weight:700;">
                                    {{ $update->progress }}%
                                </div>
                            </div>

                            <div style="margin-top:14px; color:#374151; line-height:1.9;">
                                {{ $update->description ?: 'لا يوجد وصف لهذا التحديث' }}
                            </div>

                            <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
                                @if($update->attachment)
                                    <a href="{{ asset('storage/' . $update->attachment) }}" target="_blank" class="btn btn-success btn-sm">
                                        فتح المرفق
                                    </a>
                                @endif

                                <form action="{{ route('engineering.projects.updates.destroy', [$project->id, $update->id]) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا التحديث؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                لا توجد تحديثات لهذا المشروع حتى الآن
            </div>
        @endif
    </div>
</div>
@endsection