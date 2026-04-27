@extends('layouts.app')

@section('page_title', __('projects.edit_title'))
@section('page_subtitle', __('projects.page_subtitle'))

@section('content')
<div class="page-card">
    <div class="page-header">
        <h1 class="page-title">{{ __('projects.edit_title') }}</h1>
    </div>

    <form action="{{ route('engineering-projects.update', $project->id) }}" method="POST" enctype="multipart/form-data" class="form-card">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group">
                <label>{{ __('projects.field_name') }}</label>
                <input type="text" name="name" value="{{ old('name', $project->name) }}" required>
            </div>

            <div class="form-group">
                <label>{{ __('projects.field_responsible') }}</label>
                <select name="responsible_employee_id">
                    <option value="">{{ __('projects.select_employee') }}</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('responsible_employee_id', $project->responsible_employee_id) == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>{{ __('projects.field_start') }}</label>
                <input type="date" name="start_date" value="{{ old('start_date', $project->start_date) }}" required>
            </div>

            <div class="form-group">
                <label>{{ __('projects.field_end') }}</label>
                <input type="date" name="end_date" value="{{ old('end_date', $project->end_date) }}" required>
            </div>

            <div class="form-group">
                <label>{{ __('projects.field_progress') }}</label>
                <input type="number" name="progress_percentage" min="0" max="100" value="{{ old('progress_percentage', $project->progress_percentage) }}" required>
            </div>

            @if(auth()->user()->canViewProjectFinancials())
            <div class="form-group">
                <label>{{ __('projects.field_project_value') }}</label>
                <input type="number" step="0.01" name="project_value" min="0" value="{{ old('project_value', $project->project_value) }}" required>
            </div>

            <div class="form-group">
                <label>{{ __('projects.field_expenses') }}</label>
                <input type="number" step="0.01" name="expenses" min="0" value="{{ old('expenses', $project->expenses) }}" required>
            </div>
            @elseif(auth()->user()->canViewProjectValueOnly())
            <div class="form-group">
                <label>{{ __('projects.field_project_value') }}</label>
                <input type="number" step="0.01" name="project_value" min="0" value="{{ old('project_value', $project->project_value) }}" required>
            </div>
            <input type="hidden" name="expenses" value="{{ old('expenses', $project->expenses) }}">
            @else
            <input type="hidden" name="project_value" value="{{ $project->project_value }}">
            <input type="hidden" name="expenses" value="{{ $project->expenses }}">
            @endif

            <div class="form-group">
                <label>{{ __('projects.field_status') }}</label>
                <input type="text" name="status" value="{{ old('status', $project->status) }}" required>
            </div>

            <div class="form-group form-group-full">
                <label>{{ __('projects.field_description') }}</label>
                <textarea name="description" rows="4">{{ old('description', $project->description) }}</textarea>
            </div>

            <div class="form-group form-group-full">
                <label>{{ __('projects.field_notes') }}</label>
                <textarea name="notes" rows="4">{{ old('notes', $project->notes) }}</textarea>
            </div>

            <div class="form-group form-group-full">
                <label>{{ __('projects.field_pdf_new') }}</label>
                <input type="file" name="project_pdf" accept=".pdf">
                @if($project->project_pdf)
                    <p style="margin-top:8px;">
                        {{ __('projects.current_file_available') }}
                    </p>
                @endif
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ __('projects.update') }}</button>
            <a href="{{ route('engineering-projects.index') }}" class="btn btn-secondary">{{ __('common.back') }}</a>
        </div>
    </form>
</div>
@endsection
