@extends('layouts.app')

@section('page_title', __('factory.create_order_title'))
@section('page_subtitle', __('factory.index_page_subtitle'))

@section('content')
<div class="container py-4">

    <h2 class="mb-4">{{ __('factory.create_order_title') }}</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">

            <form action="{{ route('production-orders.store') }}" method="POST" data-autofill-form-key="factory" data-autofill-endpoint="{{ route('documents.parse') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">{{ __('factory.smart_import_label') }}</label>
                    <input type="file" name="document" class="form-control" accept=".pdf,.xlsx,.csv,.jpg,.jpeg,.png,.webp" data-autofill-document-input>
                    <small data-autofill-status style="display:block; margin-top:6px; color:#94a3b8;">{{ __('factory.smart_import_hint') }}</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('factory.select_project') }}</label>
                    <select name="project_id" id="projectSelect" class="form-control" required>
                        <option value="">{{ __('factory.select_project_placeholder') }}</option>
                        @foreach($projects as $project)
                            <option 
                                value="{{ $project->id }}"
                                data-measurements='@json($project->architectMeasurements)'
                                data-design-concrete="{{ $project->required_concrete_quantity }}"
                                {{ old('project_id') == $project->id ? 'selected' : '' }}
                            >
                                {{ $project->project_code }} - {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="measurementsBox" class="mb-4" style="display:none;">
                    <h5>{{ __('factory.measurements_box_title') }}</h5>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('architect.th_type') }}</th>
                                    <th>{{ __('factory.th_element') }}</th>
                                    <th>{{ __('architect.th_length') }}</th>
                                    <th>{{ __('architect.th_width') }}</th>
                                    <th>{{ __('architect.th_height') }}</th>
                                    <th>{{ __('architect.th_count') }}</th>
                                    <th>{{ __('architect.th_area') }}</th>
                                    <th>{{ __('architect.th_volume') }}</th>
                                </tr>
                            </thead>
                            <tbody id="measurementsTable">
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('factory.field_order_number') }}</label>
                    <input type="text" name="order_number" class="form-control" value="{{ old('order_number') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('factory.product_name') }}</label>
                    <input type="text" name="product_name" class="form-control" value="{{ old('product_name') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('factory.required_quantity_readonly') }}</label>
                    <input type="number" step="0.01" name="planned_quantity" id="plannedQuantityInput" class="form-control" value="{{ old('planned_quantity') }}">
                    <small id="plannedQuantityHint" class="form-text" style="display:block; margin-top:6px; color:#94a3b8;"></small>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('factory.field_entry_date') }}</label>
                    <input type="date" name="production_start_date" class="form-control" value="{{ old('production_start_date') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('factory.expected_completion_days') }}</label>
                    <input type="date" name="expected_end_date" class="form-control" value="{{ old('expected_end_date') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('factory.field_quantity') }}</label>
                    <input type="number" step="0.01" name="daily_target" class="form-control" value="{{ old('daily_target') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('factory.field_notes') }}</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-success">{{ __('common.save') }}</button>
                <a href="{{ route('factory.index') }}" class="btn btn-secondary">{{ __('common.back') }}</a>

            </form>

        </div>
    </div>

</div>

<script>
const designQuantityReadonlyHint = @json(__('factory.design_quantity_readonly_hint'));
function refreshProjectContext() {
    let select = document.getElementById('projectSelect');
    let selected = select.options[select.selectedIndex];
    let measurements = selected.getAttribute('data-measurements');
    let designConcrete = selected.getAttribute('data-design-concrete');

    let box = document.getElementById('measurementsBox');
    let table = document.getElementById('measurementsTable');
    let pq = document.getElementById('plannedQuantityInput');
    let hint = document.getElementById('plannedQuantityHint');

    table.innerHTML = '';

    if (!measurements) {
        box.style.display = 'none';
    } else {
        let data = JSON.parse(measurements);

        if (data.length === 0) {
            box.style.display = 'none';
        } else {
            data.forEach(item => {
                table.innerHTML += `
            <tr>
                <td>${item.type ?? '-'}</td>
                <td>${item.name}</td>
                <td>${item.length ?? '-'}</td>
                <td>${item.width ?? '-'}</td>
                <td>${item.height ?? '-'}</td>
                <td>${item.quantity}</td>
                <td>${item.area}</td>
                <td>${item.volume}</td>
            </tr>
        `;
            });
            box.style.display = 'block';
        }
    }

    if (designConcrete !== null && designConcrete !== '' && parseFloat(designConcrete) > 0) {
        pq.value = designConcrete;
        pq.readOnly = true;
        hint.textContent = designQuantityReadonlyHint;
    } else {
        pq.readOnly = false;
        hint.textContent = '';
    }
}

document.getElementById('projectSelect').addEventListener('change', refreshProjectContext);
refreshProjectContext();
</script>

@endsection