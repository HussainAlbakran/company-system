{{--
    Row-based material items for architect requests.
    Expects: $itemsForForm (array of rows), $nextItemIndex (int for JS)
--}}
@php
    $rows = $itemsForForm ?? [['material_name' => '', 'description' => '', 'quantity' => '', 'unit' => 'pcs', 'notes' => '']];
@endphp

<div class="architect-mr-items-section" style="margin-top: 8px;">
    <div style="display:flex; justify-content: flex-end; align-items:center; margin-bottom:10px;">
        <button type="button" class="btn btn-primary" onclick="addItemRow()">{{ __('architect.items_add_row') }}</button>
    </div>

    <div class="table-wrap architect-mr-items-table-wrap" style="overflow-x:auto;">
        <table class="architect-mr-items-table" style="width:100%; min-width:720px;">
            <thead>
                <tr>
                    <th scope="col" style="min-width:140px;">{{ __('common.name') }}</th>
                    <th scope="col" style="min-width:160px;">{{ __('architect.th_description') }}</th>
                    <th scope="col" style="min-width:100px;">{{ __('architect.th_count') }}</th>
                    <th scope="col" style="min-width:90px;">{{ __('architect.th_unit') }}</th>
                    <th scope="col" style="min-width:140px;">{{ __('architect.th_notes') }}</th>
                    <th scope="col" style="width:100px;">{{ __('architect.items_remove') }}</th>
                </tr>
            </thead>
            <tbody id="items-body">
                @foreach($rows as $idx => $row)
                    @php
                        $row = is_array($row) ? $row : [];
                    @endphp
                    <tr class="architect-mr-item-row">
                        <td>
                            <input type="text" name="items[{{ $idx }}][material_name]" value="{{ $row['material_name'] ?? '' }}" required class="form-control" style="width:100%;">
                        </td>
                        <td>
                            <input type="text" name="items[{{ $idx }}][description]" value="{{ $row['description'] ?? '' }}" class="form-control" style="width:100%;">
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0.01" name="items[{{ $idx }}][quantity]" value="{{ $row['quantity'] ?? '' }}" required class="form-control" style="width:100%;">
                        </td>
                        <td>
                            <input type="text" name="items[{{ $idx }}][unit]" value="{{ $row['unit'] ?? 'pcs' }}" required class="form-control" style="width:100%;">
                        </td>
                        <td>
                            <input type="text" name="items[{{ $idx }}][notes]" value="{{ $row['notes'] ?? '' }}" class="form-control" style="width:100%;">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeItemRow(this)">{{ __('common.delete') }}</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
(function () {
    let itemRowIndex = {{ (int) ($nextItemIndex ?? 0) }};

    window.addItemRow = function () {
        const body = document.getElementById('items-body');
        if (!body) return;
        const row = document.createElement('tr');
        row.className = 'architect-mr-item-row';
        row.innerHTML = `
            <td><input type="text" name="items[${itemRowIndex}][material_name]" required class="form-control" style="width:100%;"></td>
            <td><input type="text" name="items[${itemRowIndex}][description]" class="form-control" style="width:100%;"></td>
            <td><input type="number" step="0.01" min="0.01" name="items[${itemRowIndex}][quantity]" required class="form-control" style="width:100%;"></td>
            <td><input type="text" name="items[${itemRowIndex}][unit]" value="pcs" required class="form-control" style="width:100%;"></td>
            <td><input type="text" name="items[${itemRowIndex}][notes]" class="form-control" style="width:100%;"></td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="removeItemRow(this)">{{ __('common.delete') }}</button></td>
        `;
        body.appendChild(row);
        itemRowIndex++;
    };

    window.removeItemRow = function (button) {
        const body = document.getElementById('items-body');
        if (!body || body.rows.length <= 1) return;
        button.closest('tr').remove();
    };
})();
</script>
