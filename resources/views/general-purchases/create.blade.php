@extends('layouts.app')

@section('page_title', __('general_purchases.create_title'))
@section('page_subtitle', __('general_purchases.create_subtitle'))

@section('content')

<div class="page-card" dir="rtl" style="text-align:right;">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 class="page-title">{{ __('general_purchases.create_title') }}</h1>
            <p style="color:#6b7280;">{{ __('general_purchases.create_subtitle') }}</p>
        </div>

        <div class="actions-row" style="flex-wrap:wrap; gap:8px;">
            <a href="{{ route('general-purchases.index') }}" class="btn btn-secondary">
                {{ __('common.back') }}
            </a>
            <a href="{{ route('assets.registration-expiring-soon') }}" class="btn btn-outline-secondary btn-sm">{{ __('general_purchases.vehicle_expiry_short') }}</a>
        </div>
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

    <form action="{{ route('general-purchases.store') }}" method="POST">
        @csrf

        <div class="form-grid">

            <div class="form-group">
                <label>{{ __('general_purchases.field_type') }}</label>
                <select name="type" id="purchaseType" onchange="toggleGeneralPurchaseFields()" required>
                    <option value="asset_purchase">{{ __('general_purchases.type_asset_single') }}</option>
                    <option value="general_maintenance">{{ __('general_purchases.type_maintenance') }}</option>
                </select>
            </div>

            <div class="form-group">
                <label>{{ __('general_purchases.field_title') }}</label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="{{ __('general_purchases.placeholder_title') }}">
            </div>

            <div class="form-group">
                <label>{{ __('general_purchases.field_category') }}</label>
                <input type="text" name="description" value="{{ old('description') }}" placeholder="{{ __('general_purchases.placeholder_category') }}">
            </div>

            <div class="form-group asset-only-field">
                <label>{{ __('general_purchases.field_quantity') }}</label>
                <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}">
            </div>

            <div class="form-group asset-only-field">
                <label>{{ __('general_purchases.field_asset_type') }}</label>
                <select name="asset_type" id="assetTypeGp">
                    <option value="general" {{ old('asset_type', 'general') === 'general' ? 'selected' : '' }}>{{ __('general_purchases.asset_general') }}</option>
                    <option value="vehicle" {{ old('asset_type') === 'vehicle' ? 'selected' : '' }}>{{ __('general_purchases.asset_vehicle') }}</option>
                </select>
            </div>

            <div class="form-group asset-only-field vehicle-gp-only">
                <label>{{ __('general_purchases.field_vehicle_type') }}</label>
                <input type="text" name="vehicle_type" value="{{ old('vehicle_type') }}" placeholder="{{ __('general_purchases.optional') }}">
            </div>

            <div class="form-group asset-only-field vehicle-gp-only">
                <label>{{ __('general_purchases.field_plate') }}</label>
                <input type="text" name="plate_number" value="{{ old('plate_number') }}">
            </div>

            <div class="form-group asset-only-field vehicle-gp-only">
                <label>{{ __('general_purchases.field_registration_no') }}</label>
                <input type="text" name="registration_number" value="{{ old('registration_number') }}">
            </div>

            <div class="form-group asset-only-field vehicle-gp-only">
                <label>{{ __('general_purchases.field_registration_expiry') }}</label>
                <input type="date" name="registration_expiry_date" value="{{ old('registration_expiry_date') }}">
            </div>

            <div class="form-group asset-only-field vehicle-gp-only">
                <label>{{ __('general_purchases.field_inspection_expiry') }}</label>
                <input type="date" name="inspection_expiry_date" value="{{ old('inspection_expiry_date') }}">
            </div>

            <div class="form-group asset-only-field vehicle-gp-only">
                <label>{{ __('general_purchases.field_color') }}</label>
                <input type="text" name="color" value="{{ old('color') }}">
            </div>

            <div class="form-group">
                <label>{{ __('general_purchases.field_cost') }}</label>
                <input type="number" step="0.01" name="cost" value="{{ old('cost') }}" required placeholder="0.00">
            </div>

            <div class="form-group">
                <label>{{ __('general_purchases.field_vendor') }}</label>
                <input type="text" name="vendor" value="{{ old('vendor') }}" placeholder="{{ __('general_purchases.placeholder_vendor_service') }}">
            </div>

            <div class="form-group">
                <label>{{ __('general_purchases.field_date') }}</label>
                <input type="date" name="purchase_date" value="{{ old('purchase_date') }}">
            </div>

            <div class="form-group form-group-full asset-only-field">
                <label>{{ __('general_purchases.field_serial') }}</label>
                <input type="text" name="serial_number" value="{{ old('serial_number') }}" placeholder="{{ __('general_purchases.placeholder_serial') }}">
            </div>

            <div class="form-group form-group-full">
                <label>{{ __('general_purchases.field_notes') }}</label>
                <textarea name="notes" rows="3">{{ old('notes') }}</textarea>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                {{ __('general_purchases.save') }}
            </button>
        </div>
    </form>

</div>

@php
    $legacyVehicle = "\u0645\u0631\u0643\u0628\u0629";
@endphp
<script>
const LEGACY_VEHICLE = @json($legacyVehicle);
function toggleVehicleGpFields() {
    const purchaseType = document.getElementById('purchaseType').value;
    const assetType = document.getElementById('assetTypeGp');
    const vehicleFields = document.querySelectorAll('.vehicle-gp-only');
    const isVehicle = assetType && (assetType.value === 'vehicle' || assetType.value === LEGACY_VEHICLE);
    const show = purchaseType === 'asset_purchase' && isVehicle;
    vehicleFields.forEach(field => {
        field.style.display = show ? 'block' : 'none';
    });
}

function toggleGeneralPurchaseFields() {
    const type = document.getElementById('purchaseType').value;
    const assetFields = document.querySelectorAll('.asset-only-field');

    assetFields.forEach(field => {
        field.style.display = type === 'asset_purchase' ? 'block' : 'none';
    });
    toggleVehicleGpFields();
}

document.addEventListener('DOMContentLoaded', function () {
    const assetTypeGp = document.getElementById('assetTypeGp');
    if (assetTypeGp) {
        assetTypeGp.addEventListener('change', toggleVehicleGpFields);
    }
    toggleGeneralPurchaseFields();
});
</script>

@endsection
