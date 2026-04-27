@extends('layouts.app')

@section('page_title', __('purchases.create_title'))
@section('page_subtitle', __('purchases.create_subtitle'))

@section('content')

<div class="page-card" dir="rtl" style="text-align:right;">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 class="page-title">{{ __('purchases.create_title') }}</h1>
            <p style="color:#6b7280;">{{ __('purchases.create_subtitle') }}</p>
        </div>

        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
            {{ __('common.back') }}
        </a>
    </div>

    @if($errors->any())
        <div class="alert-danger">
            <ul style="margin:0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('purchases.store') }}" method="POST" data-autofill-form-key="purchases" data-autofill-endpoint="{{ route('documents.parse') }}">
        @csrf

        <div class="form-grid">
            <div class="form-group form-group-full">
                <label>{{ __('purchases.smart_import_label') }}</label>
                <input type="file" name="document" accept=".pdf,.xlsx,.csv,.jpg,.jpeg,.png,.webp" data-autofill-document-input>
                <small data-autofill-status style="display:block; margin-top:6px; color:#94a3b8;">{{ __('purchases.smart_import_hint') }}</small>
            </div>

            <div class="form-group">
                <label>{{ __('purchases.field_type') }}</label>
                <select name="type" required>
                    <option value="purchase">{{ __('purchases.type_purchase') }}</option>
                    <option value="repair">{{ __('purchases.type_repair') }}</option>
                </select>
            </div>

            <div class="form-group">
                <label>{{ __('purchases.field_item_name') }}</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="{{ __('purchases.placeholder_item') }}">
            </div>

            <div class="form-group">
                <label>{{ __('purchases.field_category') }}</label>
                <input type="text" name="category" value="{{ old('category') }}" placeholder="{{ __('purchases.placeholder_category') }}">
            </div>

            <div class="form-group">
                <label>{{ __('purchases.th_quantity') }}</label>
                <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1">
            </div>

            <div class="form-group">
                <label>{{ __('purchases.field_project') }}</label>
                <select name="project_id" required>
                    <option value="">{{ __('purchases.select_project') }}</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>{{ __('purchases.field_date') }}</label>
                <input type="date" name="date" value="{{ old('date') }}">
            </div>

            <div class="form-group">
                <label>{{ __('purchases.field_vendor') }}</label>
                <input type="text" name="vendor" value="{{ old('vendor') }}" placeholder="{{ __('purchases.placeholder_vendor') }}">
            </div>

            <div class="form-group">
                <label>{{ __('purchases.field_total_cost') }}</label>
                <input type="number" step="0.01" name="total_cost" value="{{ old('total_cost') }}" placeholder="0.00">
            </div>

            <div class="form-group">
                <label>{{ __('purchases.field_unit_cost') }}</label>
                <input type="number" step="0.01" name="unit_cost" value="{{ old('unit_cost') }}" placeholder="0.00">
            </div>

            <div class="form-group form-group-full">
                <label>{{ __('common.notes') }}</label>
                <textarea name="notes" rows="3">{{ old('notes') }}</textarea>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                {{ __('purchases.save') }}
            </button>
        </div>
    </form>

</div>

@endsection
