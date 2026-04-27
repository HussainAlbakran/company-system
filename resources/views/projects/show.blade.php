@extends('layouts.app')

@section('page_title', __('projects.show_title'))
@section('page_subtitle', __('projects.show_subtitle'))

@section('content')
<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">{{ __('projects.show_title') }}</h1>
            <p style="margin:8px 0 0; color:#6b7280;">{{ __('projects.show_subtitle') }}</p>
        </div>

        <div class="actions" style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('engineering-projects.edit', $project->id) }}" class="btn btn-warning">{{ __('projects.edit') }}</a>
            <a href="{{ route('engineering-projects.index') }}" class="btn btn-secondary">{{ __('common.back') }}</a>
        </div>
    </div>

    <div class="details-grid" style="margin-bottom: 24px;">
        <div class="detail-box">
            <strong>{{ __('projects.field_name') }}</strong>
            <div>{{ $project->name }}</div>
        </div>

        <div class="detail-box">
            <strong>{{ __('projects.th_department') }}</strong>
            <div>{{ $project->department->name ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>{{ __('projects.th_responsible') }}</strong>
            <div>{{ $project->responsibleEmployee->name ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>{{ __('projects.th_status') }}</strong>
            <div>{{ $project->status ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>{{ __('projects.th_progress') }}</strong>
            <div>{{ $project->progress_percentage ?? 0 }}%</div>
        </div>

        @if(auth()->user()->canViewProjectFinancials())
        <div class="detail-box">
            <strong>{{ __('projects.th_value') }}</strong>
            <div>{{ number_format($project->project_value ?? 0, 2) }}</div>
        </div>

        <div class="detail-box">
            <strong>{{ __('projects.th_expenses') }}</strong>
            <div>{{ number_format($project->expenses ?? 0, 2) }}</div>
        </div>
        @elseif(auth()->user()->canViewProjectValueOnly())
        <div class="detail-box">
            <strong>{{ __('projects.th_value') }}</strong>
            <div>{{ number_format($project->project_value ?? 0, 2) }}</div>
        </div>
        @endif

        <div class="detail-box">
            <strong>{{ __('projects.field_start') }}</strong>
            <div>{{ $project->start_date ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>{{ __('projects.field_end') }}</strong>
            <div>{{ $project->end_date ?? '-' }}</div>
        </div>

        <div class="detail-box detail-box-full">
            <strong>{{ __('projects.field_description') }}</strong>
            <div>{{ $project->description ?? '-' }}</div>
        </div>

        <div class="detail-box detail-box-full">
            <strong>{{ __('projects.field_notes') }}</strong>
            <div>{{ $project->notes ?? '-' }}</div>
        </div>

        <div class="detail-box detail-box-full">
            <strong>{{ __('projects.field_pdf') }}</strong>
            <div style="margin-top:10px;">
                @if($project->project_pdf)
                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        <a href="{{ asset('storage/' . $project->project_pdf) }}" target="_blank" class="btn btn-success">
                            {{ __('projects.open_file') }}
                        </a>
                        <a href="{{ asset('storage/' . $project->project_pdf) }}" download class="btn btn-primary">
                            {{ __('projects.download_file') }}
                        </a>
                    </div>
                @else
                    <span style="color:#6b7280;">{{ __('projects.no_file_attached') }}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="page-card" style="margin-bottom: 24px;">
        <div class="page-header">
            <h2 style="margin:0; font-size:24px;">{{ __('projects.section_new_update') }}</h2>
            <p style="margin-top:8px; color:#6b7280;">{{ __('projects.section_new_update_sub') }}</p>
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

        <form action="{{ route('engineering-projects.updates.store', $project->id) }}" method="POST" enctype="multipart/form-data" class="form-card">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label>{{ __('projects.field_update_title') }}</label>
                    <input type="text" name="title" value="{{ old('title') }}" required>
                </div>

                <div class="form-group">
                    <label>{{ __('projects.field_update_progress') }}</label>
                    <input type="number" name="progress" min="0" max="100" value="{{ old('progress', $project->progress_percentage ?? 0) }}" required>
                </div>

                <div class="form-group form-group-full">
                    <label>{{ __('projects.field_update_description') }}</label>
                    <textarea name="description" rows="4">{{ old('description') }}</textarea>
                </div>

                <div class="form-group form-group-full">
                    <label>{{ __('projects.field_attachment') }}</label>
                    <input type="file" name="attachment">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">{{ __('projects.save_update') }}</button>
            </div>
        </form>
    </div>

    <div class="page-card">
        <div class="page-header">
            <h2 style="margin:0; font-size:24px;">{{ __('projects.section_updates') }}</h2>
            <p style="margin-top:8px; color:#6b7280;">{{ __('projects.section_updates_sub') }}</p>
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
                                        {{ $update->created_at?->format('Y-m-d H:i') ?? '-' }}
                                    </div>
                                </div>

                                <div style="background:#eff6ff; border:1px solid #bfdbfe; color:#1d4ed8; padding:8px 12px; border-radius:999px; font-weight:700;">
                                    {{ $update->progress }}%
                                </div>
                            </div>

                            <div style="margin-top:14px; color:#374151; line-height:1.9;">
                                {{ $update->description ?: __('projects.no_update_description') }}
                            </div>

                            <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
                                @if($update->attachment)
                                    <a href="{{ asset('storage/' . $update->attachment) }}" target="_blank" class="btn btn-success btn-sm">
                                        {{ __('projects.open_attachment') }}
                                    </a>
                                @endif

                                <form action="{{ route('engineering-projects.updates.destroy', [$project->id, $update->id]) }}" method="POST" onsubmit="return confirm(@json(__('projects.confirm_delete_update')))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        {{ __('common.delete') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                {{ __('projects.updates_empty') }}
            </div>
        @endif
    </div>
</div>
@endsection
