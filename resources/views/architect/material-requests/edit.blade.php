@extends('layouts.app')

@section('page_title', __('architect.material_edit_title'))
@section('page_subtitle', __('architect.material_edit_subtitle'))

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">{{ __('architect.material_edit_heading', ['id' => $materialRequest->id]) }}</h1>
            <p class="page-subtitle">{{ __('architect.project_label', ['name' => $project->name]) }}</p>
        </div>
        <div class="actions-row">
            <a href="{{ route('architect.project-material-requirements', $project) }}" class="btn btn-secondary">{{ __('common.back') }}</a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert-danger">
            <ul style="margin:0; padding-right:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($materialRequest->status === 'rejected' && $materialRequest->rejection_reason)
        <div class="alert-danger">
            <strong>{{ __('architect.purchasing_rejection_label') }}</strong>
            {{ $materialRequest->rejection_reason }}
        </div>
    @endif

    <form method="POST" action="{{ route('architect.material-requests.update', [$project, $materialRequest]) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group form-group-full">
                <label>{{ __('architect.architect_notes') }}</label>
                <textarea name="notes" rows="3">{{ old('notes', $materialRequest->notes) }}</textarea>
            </div>

            <div class="form-group form-group-full">
                <label>{{ __('architect.attachment_optional') }}</label>
                <input type="file" name="attachment" accept=".pdf,.xlsx,.csv">
                @if($materialRequest->attachment_path)
                    <div style="margin-top:8px;">
                        <a href="{{ route('architect.material-requests.attachment', $materialRequest) }}" class="btn btn-secondary btn-sm">{{ __('architect.open_current_file') }}</a>
                    </div>
                @endif
            </div>
        </div>

        <div style="margin-top:16px;">
            <h2 class="page-title" style="font-size:15px; margin-bottom:4px;">{{ __('architect.request_line_items') }}</h2>
            <p class="page-subtitle" style="font-size:13px; margin-top:0;">{{ __('architect.request_line_items_hint') }}</p>
        </div>

        @php
            $itemsForForm = old('items');
            if (! is_array($itemsForForm)) {
                $itemsForForm = [];
                foreach ($materialRequest->items as $item) {
                    $itemsForForm[] = [
                        'material_name' => $item->material_name,
                        'description' => $item->description ?? '',
                        'quantity' => $item->quantity,
                        'unit' => $item->unit,
                        'notes' => $item->notes ?? '',
                    ];
                }
            }
            if (count($itemsForForm) < 1) {
                $itemsForForm[] = ['material_name' => '', 'description' => '', 'quantity' => '', 'unit' => 'pcs', 'notes' => ''];
            }
            $rowKeys = array_map('intval', array_keys($itemsForForm));
            $nextItemIndex = (count($rowKeys) ? max($rowKeys) : -1) + 1;
        @endphp
        @include('architect.material-requests._items-table', ['itemsForForm' => $itemsForForm, 'nextItemIndex' => $nextItemIndex])

        <div class="actions-row" style="margin-top:16px;">
            <button type="submit" name="action_type" value="draft" class="btn btn-warning">{{ __('architect.save_draft') }}</button>
            <button type="submit" name="action_type" value="submit" class="btn btn-primary">{{ __('architect.submit_to_purchasing') }}</button>
        </div>
    </form>
</div>
@endsection
