@extends('layouts.app')

@section('page_title', __('assets.create_title'))
@section('page_subtitle', __('assets.create_subtitle'))

@section('content')

<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 class="page-title">{{ __('assets.create_title') }}</h1>
            <p style="color:#6b7280;">{{ __('assets.create_subtitle') }}</p>
        </div>

        <a href="{{ route('assets.index') }}" class="btn btn-secondary">
            {{ __('common.back') }}
        </a>
    </div>

    @if($errors->any())
        <div class="alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('assets.store') }}" method="POST">
        @csrf

        <div class="form-grid">

            <div class="form-group">
                <label>{{ __('assets.field_name') }}</label>
                <input type="text" name="name" required placeholder="{{ __('assets.field_name_placeholder') }}">
            </div>

            <div class="form-group">
                <label>{{ __('assets.field_asset_type') }}</label>
                <select name="asset_type" id="asset_type" required>
                    <option value="general">{{ __('assets.asset_type_general') }}</option>
                    <option value="vehicle">{{ __('assets.asset_type_vehicle') }}</option>
                </select>
            </div>

            <div class="form-group">
                <label>{{ __('assets.field_quantity') }}</label>
                <input type="number" name="quantity" value="1" min="1" required>
            </div>

            <div class="form-group">
                <label>{{ __('assets.field_serial') }}</label>
                <input type="text" name="serial_number" value="{{ old('serial_number') }}" placeholder="{{ __('assets.field_serial_help') }}">
            </div>

            <div class="form-group">
                <label>{{ __('assets.field_status') }}</label>
                <select name="status" required>
                    <option value="available">{{ __('assets.status_available') }}</option>
                    <option value="assigned">{{ __('assets.status_assigned') }}</option>
                    <option value="maintenance">{{ __('assets.status_maintenance') }}</option>
                </select>
            </div>

            <div class="form-group">
                <label>{{ __('assets.field_purchase_date') }}</label>
                <input type="date" name="purchase_date">
            </div>

            <div class="form-group vehicle-only-field" style="display:none;">
                <label>{{ __('assets.field_vehicle_type') }}</label>
                <input type="text" name="vehicle_type" value="{{ old('vehicle_type') }}" placeholder="{{ __('assets.field_vehicle_type_placeholder') }}">
            </div>

            <div class="form-group vehicle-only-field" style="display:none;">
                <label>{{ __('assets.field_plate_number') }}</label>
                <input type="text" name="plate_number" value="{{ old('plate_number') }}">
            </div>

            <div class="form-group vehicle-only-field" style="display:none;">
                <label>{{ __('assets.field_color') }}</label>
                <input type="text" name="color" value="{{ old('color') }}">
            </div>

            <div class="form-group vehicle-only-field" style="display:none;">
                <label>{{ __('assets.field_inspection_expiry') }}</label>
                <input type="date" name="inspection_expiry_date" value="{{ old('inspection_expiry_date') }}">
            </div>

            <div class="form-group vehicle-only-field" style="display:none;">
                <label>{{ __('assets.field_registration_number') }}</label>
                <input type="text" name="registration_number" value="{{ old('registration_number') }}">
            </div>

            <div class="form-group vehicle-only-field" style="display:none;">
                <label>{{ __('assets.field_registration_expiry') }}</label>
                <input type="date" name="registration_expiry_date" value="{{ old('registration_expiry_date') }}">
            </div>

            <div class="form-group form-group-full">
                <label>{{ __('assets.field_notes') }}</label>
                <textarea name="notes"></textarea>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                {{ __('assets.save_asset') }}
            </button>
        </div>

    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('asset_type');
    const vehicleFields = document.querySelectorAll('.vehicle-only-field');

    const toggleVehicleFields = function () {
        const isVehicle = typeSelect.value === 'vehicle' || typeSelect.value === 'مركبة';
        vehicleFields.forEach(function (field) {
            field.style.display = isVehicle ? 'block' : 'none';
        });
    };

    typeSelect.addEventListener('change', toggleVehicleFields);
    toggleVehicleFields();
});
</script>
@endsection
