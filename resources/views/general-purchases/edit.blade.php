@extends('layouts.app')

@section('content')

<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 class="page-title">Edit General Purchase</h1>
            <p style="color:#6b7280;">Edit asset purchase or general maintenance</p>
        </div>

        <a href="{{ route('general-purchases.index') }}" class="btn btn-secondary">
            Back
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

    <form action="{{ route('general-purchases.update', $purchase->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-grid">

            <div class="form-group">
                <label>Type</label>
                <select name="type" id="purchaseType" onchange="toggleGeneralPurchaseFields()" required>
                    <option value="asset_purchase" {{ $purchase->type == 'asset_purchase' ? 'selected' : '' }}>
                        Asset Purchase
                    </option>
                    <option value="general_maintenance" {{ $purchase->type == 'general_maintenance' ? 'selected' : '' }}>
                        General Maintenance
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label>Item / Maintenance Name</label>
                <input type="text" name="title" value="{{ old('title', $purchase->title) }}" required>
            </div>

            <div class="form-group">
                <label>Category</label>
                <input type="text" name="description" value="{{ old('description', $purchase->description) }}" placeholder="Category or section">
            </div>

            <div class="form-group asset-only-field">
                <label>Quantity</label>
                <input type="number" name="quantity" min="1" value="{{ old('quantity', $purchase->quantity ?? 1) }}">
            </div>

            <div class="form-group">
                <label>Cost</label>
                <input type="number" step="0.01" name="cost" value="{{ old('cost', $purchase->cost) }}" required>
            </div>

            <div class="form-group">
                <label>Vendor</label>
                <input type="text" name="vendor" value="{{ old('vendor', $purchase->vendor) }}">
            </div>

            <div class="form-group">
                <label>Date</label>
                <input type="date" name="purchase_date" value="{{ old('purchase_date', optional($purchase->purchase_date)->format('Y-m-d')) }}">
            </div>

            <div class="form-group form-group-full asset-only-field">
                <label>Serial Number</label>
                <input type="text" name="serial_number" value="{{ old('serial_number', optional($asset)->serial_number ?? '') }}" placeholder="Serial number for asset">
            </div>

            <div class="form-group form-group-full">
                <label>Notes</label>
                <textarea name="notes" rows="3">{{ old('notes', $purchase->notes) }}</textarea>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                Update
            </button>
        </div>
    </form>

</div>

<script>
function toggleGeneralPurchaseFields() {
    const type = document.getElementById('purchaseType').value;
    const assetFields = document.querySelectorAll('.asset-only-field');

    assetFields.forEach(field => {
        field.style.display = type === 'asset_purchase' ? 'block' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', function () {
    toggleGeneralPurchaseFields();
});
</script>

@endsection