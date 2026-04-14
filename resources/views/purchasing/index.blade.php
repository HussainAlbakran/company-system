@extends('layouts.app')

@section('content')

<div class="page-card">

    <div class="page-header">
        <h1 class="page-title">Purchasing Approval</h1>
        <p style="color:#6b7280;">Approve purchases or repairs before execution</p>
    </div>

    <form action="{{ route('purchases.store') }}" method="POST">
        @csrf

        <div class="form-grid">

            {{-- Project --}}
            <div class="form-group">
                <label>Project</label>
                <select name="project_id" required>
                    <option value="">Select Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">
                            {{ $project->project_code }} - {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Type --}}
            <div class="form-group">
                <label>Type</label>
                <select name="type" id="type" onchange="toggleFields()" required>
                    <option value="purchase">Purchase</option>
                    <option value="repair">Repair</option>
                </select>
            </div>

            {{-- 🔹 Purchase Fields --}}
            <div class="form-group purchase-field">
                <label>Item Name</label>
                <input type="text" name="title" placeholder="Cement / Steel / Equipment">
            </div>

            <div class="form-group purchase-field">
                <label>Quantity</label>
                <input type="number" name="quantity" min="1">
            </div>

            {{-- 🔹 Repair Field --}}
            <div class="form-group repair-field" style="display:none;">
                <label>Repair Item</label>
                <input type="text" name="title" placeholder="Pump repair / Machine fix">
            </div>

            {{-- Cost --}}
            <div class="form-group">
                <label>Cost</label>
                <input type="number" step="0.01" name="cost" required>
            </div>

            {{-- Date --}}
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="purchase_date">
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                Save Approval
            </button>
        </div>

    </form>

</div>

<script>
function toggleFields() {
    let type = document.getElementById('type').value;

    document.querySelectorAll('.purchase-field').forEach(el => {
        el.style.display = (type === 'purchase') ? 'block' : 'none';
    });

    document.querySelectorAll('.repair-field').forEach(el => {
        el.style.display = (type === 'repair') ? 'block' : 'none';
    });
}
</script>

@endsection