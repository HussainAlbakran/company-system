@extends('layouts.app')

@section('page_title', __('project_reports.create_title'))
@section('page_subtitle', __('project_reports.create_subtitle'))

@section('content')
<div class="dashboard-stack">
    <section class="dashboard-panel">
        <div class="panel-head" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
            <div>
                <h2 class="panel-title">{{ __('project_reports.create_title') }}</h2>
                <p class="panel-subtitle">{{ __('project_reports.create_subtitle') }}</p>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                @if(auth()->user()->canViewProjectReportsBoard())
                    <a href="{{ route('project-reports.board') }}" class="btn btn-secondary btn-sm">
                        {{ __('project_reports.board_btn') }}
                    </a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="alert-success" style="margin-top:12px;">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert-danger" style="margin-top:12px;">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('project-reports.store') }}" enctype="multipart/form-data" style="margin-top:16px;">
            @csrf

            <div class="form-grid">
                <div class="form-group" style="grid-column:1 / -1;">
                    <label for="project_search">{{ __('project_reports.field_project_search') }}</label>
                    <input
                        type="text"
                        id="project_search"
                        placeholder="{{ __('project_reports.field_project_placeholder') }}"
                        autocomplete="off"
                    >
                </div>

                <div class="form-group" style="grid-column:1 / -1;">
                    <label for="project_id">{{ __('project_reports.field_project') }}</label>
                    <select name="project_id" id="project_id" required>
                        <option value="">{{ __('project_reports.field_select_project') }}</option>
                        @foreach($projects as $project)
                            <option
                                value="{{ $project->id }}"
                                data-label="{{ strtolower(($project->name ?? '').' '.($project->project_code ?? '').' '.($project->client_name ?? '')) }}"
                                {{ (string) old('project_id', $selectedProjectId) === (string) $project->id ? 'selected' : '' }}
                            >
                                {{ $project->name }}
                                @if($project->project_code)
                                    ({{ $project->project_code }})
                                @endif
                                @if($project->client_name)
                                    — {{ $project->client_name }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="report_type">{{ __('project_reports.field_type') }}</label>
                    <select name="report_type" id="report_type" required>
                        @foreach($reportTypes as $type)
                            <option value="{{ $type }}" {{ old('report_type') === $type ? 'selected' : '' }}>
                                {{ __('project_reports.types.'.$type) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="report_date">{{ __('project_reports.field_date') }}</label>
                    <input type="date" name="report_date" id="report_date" value="{{ old('report_date', now()->toDateString()) }}" required>
                </div>

                <div class="form-group" style="grid-column:1 / -1;">
                    <label for="file">{{ __('project_reports.field_file') }}</label>
                    <input type="file" name="file" id="file" required>
                    <small class="text-muted d-block mt-1">{{ __('project_reports.field_file_hint', ['max' => $maxUploadMb ?? '2']) }}</small>
                </div>

                <div class="form-group" style="grid-column:1 / -1;">
                    <label for="notes">{{ __('project_reports.field_notes') }}</label>
                    <textarea name="notes" id="notes" rows="3">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="actions-row" style="margin-top:16px;">
                <button type="submit" class="btn btn-primary">{{ __('project_reports.save') }}</button>
            </div>
        </form>
    </section>
</div>

<script>
(() => {
    const search = document.getElementById('project_search');
    const select = document.getElementById('project_id');
    if (!search || !select) return;

    search.addEventListener('input', () => {
        const q = (search.value || '').trim().toLowerCase();
        Array.from(select.options).forEach((opt, idx) => {
            if (idx === 0) return;
            const label = opt.getAttribute('data-label') || '';
            opt.hidden = q !== '' && !label.includes(q);
        });
    });
})();
</script>
@endsection
