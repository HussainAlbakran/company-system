@extends('layouts.app')

@section('page_title', __('projects.create_title'))
@section('page_subtitle', __('projects.page_subtitle'))

@section('content')
<div class="page-card">

    <div class="page-header">
        <h1 class="page-title">➕ {{ __('projects.create_title') }}</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom:15px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('engineering-projects.store') }}" method="POST" enctype="multipart/form-data" data-autofill-form-key="projects" data-autofill-endpoint="{{ route('documents.parse') }}">
        @csrf

        <div class="form-grid">
            <div class="form-group form-group-full">
                <label>{{ __('projects.smart_import_label') }}</label>
                <input type="file" name="document" accept=".pdf,.xlsx,.csv,.jpg,.jpeg,.png,.webp" data-autofill-document-input>
                <small data-autofill-status style="display:block; margin-top:6px; color:#94a3b8;">{{ __('projects.smart_import_hint') }}</small>
            </div>

            <div class="form-group">
                <label>{{ __('projects.field_name') }}</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label>{{ __('projects.field_responsible') }}</label>
                <select name="responsible_employee_id">
                    <option value="">{{ __('projects.select_employee') }}</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}"
                            {{ old('responsible_employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>{{ __('projects.field_start') }}</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" required>
            </div>

            <div class="form-group">
                <label>{{ __('projects.field_end') }}</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" required>
            </div>

            <div class="form-group">
                <label>{{ __('projects.field_progress') }}</label>
                <input type="number" name="progress_percentage" min="0" max="100"
                       value="{{ old('progress_percentage', 0) }}">
            </div>

            @if(auth()->user()->canViewProjectFinancials())
            <div class="form-group">
                <label>{{ __('projects.field_project_value') }}</label>
                <input type="number" step="0.01" name="project_value"
                       value="{{ old('project_value', 0) }}">
            </div>

            <div class="form-group">
                <label>{{ __('projects.field_expenses') }}</label>
                <input type="number" step="0.01" name="expenses"
                       value="{{ old('expenses', 0) }}">
            </div>
            @elseif(auth()->user()->canViewProjectValueOnly())
            <div class="form-group">
                <label>{{ __('projects.field_project_value') }}</label>
                <input type="number" step="0.01" name="project_value"
                       value="{{ old('project_value', 0) }}">
            </div>
            <input type="hidden" name="expenses" value="{{ old('expenses', 0) }}">
            @else
            <input type="hidden" name="project_value" value="0">
            <input type="hidden" name="expenses" value="0">
            @endif

            <div class="form-group">
                <label>{{ __('projects.field_status') }}</label>
                <input type="text" name="status"
                       value="{{ old('status', 'ongoing') }}">
            </div>

            <div class="form-group form-group-full">
                <label>{{ __('projects.field_description') }}</label>
                <textarea name="description">{{ old('description') }}</textarea>
            </div>

            <div class="form-group form-group-full">
                <label>{{ __('projects.field_notes') }}</label>
                <textarea name="notes">{{ old('notes') }}</textarea>
            </div>

            <div class="form-group form-group-full">
                <label>{{ __('projects.field_pdf') }}</label>
                <input type="file" name="project_pdf" accept="application/pdf">
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ __('projects.save') }}</button>
            <a href="{{ route('engineering-projects.index') }}" class="btn btn-secondary">{{ __('common.back') }}</a>
        </div>

    </form>

</div>
@endsection
