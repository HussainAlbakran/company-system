@extends('layouts.app')

@section('page_title', __('purchases.edit_purchase_title'))
@section('page_subtitle', __('purchases.edit_purchase_subtitle'))

@section('content')

<div class="page-card">

    <div class="page-header">
        <h2>{{ __('purchases.edit_purchase_title') }}</h2>
        <p>{{ __('purchases.edit_purchase_subtitle') }}</p>
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

    <form action="{{ route('purchases.update', $purchase->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-grid">

            <div class="form-group">
                <label>{{ __('purchases.field_project') }}</label>
                <select name="project_id" required>
                    <option value="">{{ __('purchases.select_project') }}</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}"
                            {{ old('project_id', $purchase->project_id) == $project->id ? 'selected' : '' }}>
                            {{ $project->project_code }} - {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>{{ __('purchases.field_operation_type') }}</label>
                <select name="type" required>
                    <option value="purchase" {{ old('type', $purchase->type) == 'purchase' ? 'selected' : '' }}>{{ __('purchases.type_purchase') }}</option>
                    <option value="repair" {{ old('type', $purchase->type) == 'repair' ? 'selected' : '' }}>{{ __('purchases.type_repair_alt') }}</option>
                </select>
            </div>

            <div class="form-group">
                <label>{{ __('purchases.field_line_title') }}</label>
                <input type="text" name="title" value="{{ old('title', $purchase->title) }}" required>
            </div>

            <div class="form-group">
                <label>{{ __('purchases.field_cost') }}</label>
                <input type="number" step="0.01" name="cost" value="{{ old('cost', $purchase->cost) }}" required>
            </div>

            <div class="form-group">
                <label>{{ __('purchases.field_vendor_party') }}</label>
                <input type="text" name="vendor" value="{{ old('vendor', $purchase->vendor) }}">
            </div>

            <div class="form-group">
                <label>{{ __('purchases.field_operation_date') }}</label>
                <input type="date" name="purchase_date" value="{{ old('purchase_date', optional($purchase->purchase_date)->format('Y-m-d') ?? $purchase->purchase_date) }}">
            </div>

            <div class="form-group form-group-full">
                <label>{{ __('purchases.field_description') }}</label>
                <textarea name="description">{{ old('description', $purchase->description) }}</textarea>
            </div>

            <div class="form-group form-group-full">
                <label>{{ __('common.notes') }}</label>
                <textarea name="notes">{{ old('notes', $purchase->notes) }}</textarea>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">{{ __('purchases.save_changes') }}</button>
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">{{ __('common.back') }}</a>
        </div>

    </form>

</div>

@endsection
