@php
    $isEdit = ! empty($installationRequest);
@endphp

<form method="POST"
      action="{{ $isEdit ? route('installations.factory-requests.update', [$project, $installationRequest]) : route('installations.factory-requests.store', $project) }}"
      id="installation-factory-request-form">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <x-ui.card :title="__('factory.form_project_data_title')" :subtitle="__('factory.form_project_data_sub')">
        <div class="details-grid">
            <div class="detail-box">
                <strong>{{ __('factory.project_code') }}</strong>
                <div>{{ $project->project_code }}</div>
            </div>
            <div class="detail-box">
                <strong>{{ __('factory.project_name') }}</strong>
                <div><strong>{{ $project->name }}</strong></div>
            </div>
            <div class="detail-box">
                <strong>{{ __('factory.client') }}</strong>
                <div>{{ $project->client_name ?? '-' }}</div>
            </div>
        </div>
    </x-ui.card>

    <x-ui.card :title="__('factory.form_line_items_title')" :subtitle="__('factory.form_line_items_sub')">
        <div class="table-wrap" style="overflow-x:auto;">
            <table id="items-table" class="w-full">
                <thead>
                    <tr>
                        <th>{{ __('factory.th_item_name') }}</th>
                        <th>{{ __('factory.th_description') }}</th>
                        <th>{{ __('factory.field_quantity') }}</th>
                        <th>{{ __('architect.th_unit') }}</th>
                        <th>{{ __('factory.th_reason') }}</th>
                        <th>{{ __('factory.field_notes') }}</th>
                    </tr>
                </thead>
                <tbody id="items-body">
                    @foreach($items as $index => $item)
                        <tr class="item-row">
                            <td>
                                <input type="text" name="items[{{ $index }}][item_name]" value="{{ old('items.'.$index.'.item_name', $item->item_name ?? '') }}" class="form-control" style="min-width:140px;">
                            </td>
                            <td>
                                <input type="text" name="items[{{ $index }}][description]" value="{{ old('items.'.$index.'.description', $item->description ?? '') }}" class="form-control" style="min-width:120px;">
                            </td>
                            <td>
                                <input type="number" name="items[{{ $index }}][quantity]" value="{{ old('items.'.$index.'.quantity', $item->quantity ?? 1) }}" min="1" class="form-control" style="width:88px;">
                            </td>
                            <td>
                                <input type="text" name="items[{{ $index }}][unit]" value="{{ old('items.'.$index.'.unit', $item->unit ?? '') }}" class="form-control" style="width:88px;" placeholder="{{ __('factory.unit_placeholder_example') }}">
                            </td>
                            <td>
                                <input type="text" name="items[{{ $index }}][reason]" value="{{ old('items.'.$index.'.reason', $item->reason ?? '') }}" class="form-control" style="min-width:120px;">
                            </td>
                            <td>
                                <input type="text" name="items[{{ $index }}][notes]" value="{{ old('items.'.$index.'.notes', $item->notes ?? '') }}" class="form-control" style="min-width:120px;">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="actions-row" style="margin-top:12px;">
            <button type="button" class="btn btn-secondary btn-sm" id="add-item-row">{{ __('factory.add_row') }}</button>
        </div>

        <div class="form-group form-group-full" style="margin-top:16px;">
            <label>{{ __('factory.general_request_notes') }}</label>
            <textarea name="notes" rows="3" class="form-control">{{ old('notes', $isEdit ? $installationRequest->notes : '') }}</textarea>
        </div>
    </x-ui.card>

    <div class="actions-row" style="margin-top:16px;">
        <button type="submit" name="action" value="draft" class="btn btn-secondary">{{ __('factory.save_draft') }}</button>
        <button type="submit" name="action" value="submit" class="btn btn-primary">{{ __('factory.submit_to_factory') }}</button>
        <a href="{{ route('installations.show', $project) }}" class="btn btn-outline-secondary">{{ __('factory.cancel') }}</a>
    </div>
</form>

<script>
(function () {
    let rowIndex = {{ $items->count() }};
    const tbody = document.getElementById('items-body');
    const addBtn = document.getElementById('add-item-row');

    function addRow() {
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td><input type="text" name="items[${rowIndex}][item_name]" class="form-control" style="min-width:140px;"></td>
            <td><input type="text" name="items[${rowIndex}][description]" class="form-control" style="min-width:120px;"></td>
            <td><input type="number" name="items[${rowIndex}][quantity]" value="1" min="1" class="form-control" style="width:88px;"></td>
            <td><input type="text" name="items[${rowIndex}][unit]" class="form-control" style="width:88px;" placeholder="@js(__('factory.unit_placeholder_example'))"></td>
            <td><input type="text" name="items[${rowIndex}][reason]" class="form-control" style="min-width:120px;"></td>
            <td><input type="text" name="items[${rowIndex}][notes]" class="form-control" style="min-width:120px;"></td>
        `;
        tbody.appendChild(tr);
        rowIndex++;
    }

    if (addBtn) {
        addBtn.addEventListener('click', addRow);
    }
})();
</script>
