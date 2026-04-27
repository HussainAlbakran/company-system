@extends('layouts.app')

@php
    $slug = str_replace('-', '_', $sectionKey);
    $sectionTransKey = 'warehouse.section_'.$slug;
    $sectionLabel = __($sectionTransKey);
    if ($sectionLabel === $sectionTransKey) {
        $sectionLabel = __('warehouse.section_unknown');
    }
@endphp

@section('page_title', __('warehouse.edit_title'))
@section('page_subtitle', __('warehouse.edit_subtitle', ['section' => $sectionLabel]))

@section('content')

<div class="page-card" dir="rtl" style="text-align:right;">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">{{ __('warehouse.edit_title') }}</h1>
            <p style="margin:8px 0 0; color:#6b7280;">
                {{ __('warehouse.edit_subtitle', ['section' => $sectionLabel]) }}
            </p>
        </div>

        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            {{ __('warehouse.back') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
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

    <form action="{{ route('warehouse.update', $item->id) }}" method="POST" data-autofill-form-key="warehouse" data-autofill-endpoint="{{ route('documents.parse') }}">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group form-group-full">
                <label>{{ __('warehouse.smart_import_label') }}</label>
                <input type="file" name="document" accept=".pdf,.xlsx,.csv,.jpg,.jpeg,.png,.webp" data-autofill-document-input>
                <small data-autofill-status style="display:block; margin-top:6px; color:#94a3b8;">{{ __('warehouse.smart_import_hint') }}</small>
            </div>

            <div class="form-group">
                <label>{{ __('warehouse.field_name') }}</label>
                <input type="text" name="name" value="{{ old('name', $item->name) }}">
            </div>

            <div class="form-group">
                <label>{{ __('warehouse.th_quantity') }}</label>
                <input type="text" name="quantity" value="{{ old('quantity', $item->quantity) }}">
            </div>

            <div class="form-group">
                <label>{{ __('warehouse.th_unit') }}</label>
                <input type="text" name="unit" value="{{ old('unit', $item->unit) }}">
            </div>

            <div class="form-group form-group-full">
                <label>{{ __('warehouse.th_notes') }}</label>
                <textarea name="notes" rows="4">{{ old('notes', $item->notes) }}</textarea>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                {{ __('warehouse.save_changes') }}
            </button>
        </div>
    </form>

</div>

@endsection
