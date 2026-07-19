@extends('layouts.app')

@section('page_title', __('architect.task_show_page_title'))
@section('page_subtitle', __('architect.task_show_page_subtitle'))

@section('content')
@php
    $architectI18n = [
        'placeholderTypeExample' => __('architect.placeholder_type_example'),
        'placeholderElementName' => __('architect.placeholder_element_name'),
        'placeholderNotes' => __('architect.placeholder_notes'),
        'unitM' => __('architect.unit_m'),
        'unitCm' => __('architect.unit_cm'),
        'unitMm' => __('architect.unit_mm'),
        'delete' => __('common.delete'),
        'confirmDeleteMeasurement' => __('architect.confirm_delete_measurement'),
    ];
@endphp

<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h2>{{ __('architect.task_header_title') }}</h2>
            <p>{{ __('architect.task_header_sub') }}</p>
        </div>

        <div class="actions-row">
            <a href="{{ route('architect.project-material-requirements', $project) }}" class="btn btn-primary">{{ __('architect.request_materials') }}</a>
            <a href="{{ route('architect-tasks.index') }}" class="btn btn-secondary">{{ __('common.back') }}</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert-danger">
            <ul style="margin:0; padding-right:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="page-card" style="margin-bottom:24px;">
        <div class="page-header"><h2 style="margin:0; font-size:22px;">{{ __('architect.section_project_data') }}</h2></div>
        <div class="details-grid">
            <div class="detail-box"><strong>{{ __('architect.th_project_code') }}</strong><div>{{ $project->project_code }}</div></div>
            <div class="detail-box"><strong>{{ __('architect.th_project_name') }}</strong><div>{{ $project->name }}</div></div>
            <div class="detail-box"><strong>{{ __('architect.th_client') }}</strong><div>{{ $project->client_name }}</div></div>
            <div class="detail-box"><strong>{{ __('architect.main_contractor') }}</strong><div>{{ $project->main_contractor ?? '-' }}</div></div>
            <div class="detail-box"><strong>{{ __('architect.current_stage') }}</strong><div><span class="badge badge-blue">{{ $project->current_stage }}</span></div></div>
            <div class="detail-box"><strong>{{ __('architect.status') }}</strong><div>{{ $project->status }}</div></div>
            <div class="detail-box"><strong>{{ __('architect.required_concrete') }}</strong><div>{{ $project->required_concrete_quantity !== null ? number_format((float) $project->required_concrete_quantity, 2) : '—' }}</div></div>
            <div class="detail-box detail-box-full"><strong>{{ __('architect.project_description') }}</strong><div>{{ $project->description ?? '-' }}</div></div>
        </div>
    </div>

    <div class="page-card" style="margin-bottom:24px;">
        <div class="page-header"><h2 style="margin:0; font-size:22px;">{{ __('architect.section_drawing_planning') }}</h2></div>
        <form action="{{ route('architect-tasks.update', $project->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label>{{ __('architect.field_drawing_type') }}</label>
                    <input type="text" name="drawing_type" value="{{ old('drawing_type', $architectTask->drawing_type) }}">
                </div>
                <div class="form-group">
                    <label>{{ __('architect.field_drawing_status') }}</label>
                    <select name="drawing_status" required>
                        <option value="pending" {{ old('drawing_status', $architectTask->drawing_status) == 'pending' ? 'selected' : '' }}>{{ __('architect.status_pending') }}</option>
                        <option value="in_progress" {{ old('drawing_status', $architectTask->drawing_status) == 'in_progress' ? 'selected' : '' }}>{{ __('architect.status_in_progress') }}</option>
                        <option value="completed" {{ old('drawing_status', $architectTask->drawing_status) == 'completed' ? 'selected' : '' }}>{{ __('architect.status_completed') }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ __('architect.field_planning_status') }}</label>
                    <select name="planning_status" required>
                        <option value="pending" {{ old('planning_status', $architectTask->planning_status) == 'pending' ? 'selected' : '' }}>{{ __('architect.status_pending') }}</option>
                        <option value="in_progress" {{ old('planning_status', $architectTask->planning_status) == 'in_progress' ? 'selected' : '' }}>{{ __('architect.status_in_progress') }}</option>
                        <option value="completed" {{ old('planning_status', $architectTask->planning_status) == 'completed' ? 'selected' : '' }}>{{ __('architect.status_completed') }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>{{ __('architect.field_drawing_file') }}</label>
                    <input type="file" name="drawing_file">
                    @if($architectTask->drawing_file)
                        <div style="margin-top:8px;"><a href="{{ asset('storage/' . $architectTask->drawing_file) }}" target="_blank" class="btn btn-sm btn-primary">{{ __('architect.open_current_file') }}</a></div>
                    @endif
                </div>
                <div class="form-group">
                    <label>{{ __('architect.field_planning_file') }}</label>
                    <input type="file" name="planning_file">
                    @if($architectTask->planning_file)
                        <div style="margin-top:8px;"><a href="{{ asset('storage/' . $architectTask->planning_file) }}" target="_blank" class="btn btn-sm btn-primary">{{ __('architect.open_current_file') }}</a></div>
                    @endif
                </div>
                <div class="form-group form-group-full">
                    <label>{{ __('architect.required_concrete_field') }}</label>
                    <input type="number" step="0.01" min="0" name="required_concrete_quantity" value="{{ old('required_concrete_quantity', $project->required_concrete_quantity) }}" placeholder="0.00">
                    <p class="page-subtitle" style="margin-top:6px; font-size:12px;">{{ __('architect.required_concrete_hint') }}</p>
                </div>
                <div class="form-group form-group-full">
                    <label>{{ __('architect.architect_notes') }}</label>
                    <textarea name="notes">{{ old('notes', $architectTask->notes) }}</textarea>
                </div>
            </div>
            <div class="form-actions"><button type="submit" class="btn btn-primary">{{ __('architect.save_architect_data') }}</button></div>
        </form>
    </div>

    <div class="page-card" style="margin-bottom:24px;">
        <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
            <div>
                <h2 style="margin:0; font-size:22px;">{{ __('architect.measurement_entry_title') }}</h2>
                <p style="margin:8px 0 0; color:#6b7280;">{{ __('architect.measurement_entry_sub') }}</p>
            </div>
            <button type="button" class="btn btn-success" onclick="addMeasurementRow()">+ {{ __('architect.add_row') }}</button>
        </div>

        <form action="{{ route('architect.measurements.store', $project->id) }}" method="POST" id="bulkMeasurementForm">
            @csrf
            <div class="table-wrap">
                <table id="measurement-entry-table">
                    <thead>
                        <tr>
                            <th>{{ __('architect.th_type') }}</th>
                            <th>{{ __('architect.th_element_name') }}</th>
                            <th>{{ __('architect.th_length') }}</th>
                            <th>{{ __('architect.th_width') }}</th>
                            <th>{{ __('architect.th_height') }}</th>
                            <th>{{ __('architect.th_count') }}</th>
                            <th>{{ __('architect.th_unit') }}</th>
                            <th>{{ __('architect.th_price') }}</th>
                            <th>{{ __('architect.th_notes') }}</th>
                            <th>{{ __('architect.th_delete_row') }}</th>
                        </tr>
                    </thead>
                    <tbody id="measurement-entry-body">
                        <tr>
                            <td><input type="text" name="rows[0][type]" placeholder="{{ __('architect.placeholder_type_example') }}"></td>
                            <td><input type="text" name="rows[0][name]" placeholder="{{ __('architect.placeholder_element_name') }}" required></td>
                            <td><input type="number" step="0.01" name="rows[0][length]" placeholder="0.00"></td>
                            <td><input type="number" step="0.01" name="rows[0][width]" placeholder="0.00"></td>
                            <td><input type="number" step="0.01" name="rows[0][height]" placeholder="0.00"></td>
                            <td><input type="number" name="rows[0][quantity]" value="1" min="1" required></td>
                            <td>
                                <select name="rows[0][unit]">
                                    <option value="m">{{ __('architect.unit_m') }}</option>
                                    <option value="cm">{{ __('architect.unit_cm') }}</option>
                                    <option value="mm">{{ __('architect.unit_mm') }}</option>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" name="rows[0][price]" placeholder="0.00"></td>
                            <td><textarea name="rows[0][notes]" rows="1" placeholder="{{ __('architect.placeholder_notes') }}"></textarea></td>
                            <td><button type="button" class="btn btn-danger btn-sm" onclick="removeMeasurementRow(this)">{{ __('common.delete') }}</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-actions" style="margin-top:16px;"><button type="submit" class="btn btn-primary">{{ __('architect.save_all_rows') }}</button></div>
        </form>
    </div>

    <div class="page-card" style="margin-bottom:24px;">
        <div class="page-header"><h2 style="margin:0; font-size:22px;">{{ __('architect.saved_measurements_title') }}</h2></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('architect.th_type') }}</th>
                        <th>{{ __('architect.th_element_name') }}</th>
                        <th>{{ __('architect.th_length') }}</th>
                        <th>{{ __('architect.th_width') }}</th>
                        <th>{{ __('architect.th_height') }}</th>
                        <th>{{ __('architect.th_count') }}</th>
                        <th>{{ __('architect.th_unit') }}</th>
                        <th>{{ __('architect.th_area') }}</th>
                        <th>{{ __('architect.th_volume') }}</th>
                        <th>{{ __('architect.th_price') }}</th>
                        <th>{{ __('architect.th_notes') }}</th>
                        <th>{{ __('architect.th_delete') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($measurements as $measurement)
                        <tr>
                            <td>{{ $measurement->id }}</td>
                            <td>{{ $measurement->type ?? '-' }}</td>
                            <td>{{ $measurement->name }}</td>
                            <td>{{ $measurement->length }}</td>
                            <td>{{ $measurement->width }}</td>
                            <td>{{ $measurement->height }}</td>
                            <td>{{ $measurement->quantity }}</td>
                            <td>{{ $measurement->unit ?? 'm' }}</td>
                            <td>{{ $measurement->area }}</td>
                            <td>{{ $measurement->volume }}</td>
                            <td>{{ $measurement->price ?? '-' }}</td>
                            <td>{{ $measurement->notes ?? '-' }}</td>
                            <td>
                                <form action="{{ route('architect.measurements.destroy', $measurement->id) }}" method="POST" onsubmit="return confirm(@json(__('architect.confirm_delete_measurement')))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">{{ __('common.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="13" class="empty-row">{{ __('architect.measurements_empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="page-card">
        <div class="page-header">
            <h2 style="margin:0; font-size:22px;">{{ __('architect.approval_section_title') }}</h2>
            <p style="margin:8px 0 0; color:#6b7280;">{{ __('architect.approval_section_sub') }}</p>
        </div>
        <div class="form-actions">
            <form action="{{ route('architect-tasks.sendToFactory', $project->id) }}" method="POST" onsubmit="return confirm(@json(__('architect.confirm_send_factory_installation')))">
                @csrf
                <button type="submit" class="btn btn-primary">{{ __('architect.send_designs_factory_installation') }}</button>
            </form>
        </div>
    </div>
</div>

<script>
let measurementRowIndex = 1;
const architectI18n = @json($architectI18n);

function addMeasurementRow() {
    const tbody = document.getElementById('measurement-entry-body');
    const row = document.createElement('tr');

    row.innerHTML = `
        <td><input type="text" name="rows[${measurementRowIndex}][type]" placeholder="${architectI18n.placeholderTypeExample}"></td>
        <td><input type="text" name="rows[${measurementRowIndex}][name]" placeholder="${architectI18n.placeholderElementName}" required></td>
        <td><input type="number" step="0.01" name="rows[${measurementRowIndex}][length]" placeholder="0.00"></td>
        <td><input type="number" step="0.01" name="rows[${measurementRowIndex}][width]" placeholder="0.00"></td>
        <td><input type="number" step="0.01" name="rows[${measurementRowIndex}][height]" placeholder="0.00"></td>
        <td><input type="number" name="rows[${measurementRowIndex}][quantity]" value="1" min="1" required></td>
        <td>
            <select name="rows[${measurementRowIndex}][unit]">
                <option value="m">${architectI18n.unitM}</option>
                <option value="cm">${architectI18n.unitCm}</option>
                <option value="mm">${architectI18n.unitMm}</option>
            </select>
        </td>
        <td><input type="number" step="0.01" name="rows[${measurementRowIndex}][price]" placeholder="0.00"></td>
        <td><textarea name="rows[${measurementRowIndex}][notes]" rows="1" placeholder="${architectI18n.placeholderNotes}"></textarea></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="removeMeasurementRow(this)">${architectI18n.delete}</button></td>
    `;

    tbody.appendChild(row);
    measurementRowIndex++;
}

function removeMeasurementRow(button) {
    const tbody = document.getElementById('measurement-entry-body');
    if (tbody.rows.length > 1) {
        if (confirm(architectI18n.confirmDeleteMeasurement)) {
            button.closest('tr').remove();
        }
    }
}
</script>
@endsection
