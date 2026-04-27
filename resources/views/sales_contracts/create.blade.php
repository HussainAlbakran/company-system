@extends('layouts.app')

@section('page_title', __('contracts.create_title'))
@section('page_subtitle', __('contracts.page_subtitle'))

@section('content')

<div class="container">
    <h2 class="mb-4">{{ __('contracts.create_title') }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sales-contracts.store') }}" method="POST" enctype="multipart/form-data" data-autofill-form-key="contracts" data-autofill-endpoint="{{ route('documents.parse') }}">
        @csrf

        <div class="row">
            <div class="col-md-12 mb-3">
                <label>{{ __('contracts.smart_import_label') }}</label>
                <input type="file" name="document" class="form-control" accept=".pdf,.xlsx,.csv,.jpg,.jpeg,.png,.webp" data-autofill-document-input>
                <small data-autofill-status style="display:block; margin-top:6px; color:#94a3b8;">{{ __('contracts.smart_import_hint') }}</small>
            </div>

            <div class="col-md-6 mb-3">
                <label>{{ __('contracts.field_contract_no') }}</label>
                <input type="text" name="contract_no" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>{{ __('contracts.field_contract_date') }}</label>
                <input type="date" name="contract_date" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>{{ __('contracts.field_client_name') }}</label>
                <input type="text" name="client_name" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>{{ __('contracts.field_main_contractor') }}</label>
                <input type="text" name="main_contractor" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
                <label>{{ __('contracts.field_project_name') }}</label>
                <input type="text" name="project_name" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>{{ __('contracts.field_project_location') }}</label>
                <input type="text" name="project_location" class="form-control">
            </div>

            @php
                $u = auth()->user();
                $finFull = $u->canViewProjectFinancials();
                $valOnly = $u->canViewProjectValueOnly();
            @endphp

            @if($finFull)
            <div class="col-md-6 mb-3">
                <label>{{ __('contracts.field_project_value') }}</label>
                <input type="number" step="0.01" name="project_value" id="project_value" class="form-control">
            </div>
            @elseif($valOnly)
            <div class="col-md-6 mb-3">
                <label>{{ __('contracts.field_project_value') }}</label>
                <input type="number" step="0.01" name="project_value" id="project_value" class="form-control">
            </div>
            @else
            <input type="hidden" name="project_value" id="project_value" value="0">
            @endif

            <div class="col-md-4 mb-3">
                <label>{{ __('contracts.field_expected_start') }}</label>
                <input type="date" name="expected_start_date" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('contracts.field_actual_start') }}</label>
                <input type="date" name="actual_start_date" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('contracts.field_expected_end') }}</label>
                <input type="date" name="expected_end_date" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
                <label>{{ __('contracts.field_payment_type') }}</label>
                <select name="payment_type" id="payment_type" class="form-control" onchange="togglePaymentFields()" required>
                    <option value="full">{{ __('contracts.payment_full') }}</option>
                    <option value="installments">{{ __('contracts.payment_installments') }}</option>
                </select>
            </div>

            @if($finFull)
            <div class="col-md-6 mb-3 full-payment-field">
                <label>{{ __('contracts.field_full_payment_amount') }}</label>
                <input type="number" step="0.01" name="full_payment_amount" class="form-control" placeholder="{{ __('contracts.placeholder_amount_paid') }}">
            </div>

            <div class="col-md-6 mb-3 installment-field" style="display:none;">
                <label>{{ __('contracts.field_first_payment_title') }}</label>
                <input type="text" name="first_payment_title" class="form-control" placeholder="{{ __('contracts.placeholder_first_payment') }}">
            </div>

            <div class="col-md-6 mb-3 installment-field" style="display:none;">
                <label>{{ __('contracts.field_first_payment_pct') }}</label>
                <input type="number" step="0.01" name="first_payment_percentage" id="first_payment_percentage" class="form-control" placeholder="{{ __('contracts.placeholder_pct') }}" oninput="calculateFirstPaymentAmount()">
            </div>

            <div class="col-md-6 mb-3 installment-field" style="display:none;">
                <label>{{ __('contracts.field_first_payment_amount') }}</label>
                <input type="number" step="0.01" name="first_payment_amount" id="first_payment_amount" class="form-control" placeholder="{{ __('contracts.placeholder_auto_calc') }}">
            </div>

            <div class="col-md-6 mb-3 installment-field" style="display:none;">
                <label>{{ __('contracts.field_first_payment_due') }}</label>
                <input type="date" name="first_payment_due_date" class="form-control">
            </div>
            @else
            <input type="hidden" name="full_payment_amount" value="0">
            <input type="hidden" name="first_payment_title" value="">
            <input type="hidden" name="first_payment_percentage" value="0">
            <input type="hidden" name="first_payment_amount" value="0">
            <input type="hidden" name="first_payment_due_date" value="">
            @endif

            <div class="col-md-12 mb-3">
                <label>{{ __('contracts.field_project_description') }}</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>

            <div class="col-md-12 mb-3">
                <label>{{ __('contracts.field_notes') }}</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
            </div>

            <div class="col-md-12 mb-3">
                <label>{{ __('contracts.field_contract_file') }}</label>
                <input type="file" name="contract_file" class="form-control">
            </div>

        </div>

        <button type="submit" class="btn btn-primary">
            {{ __('contracts.save_contract') }}
        </button>

        <a href="{{ route('sales-contracts.index') }}" class="btn btn-secondary">
            {{ __('common.back') }}
        </a>

    </form>
</div>

<script>
    function togglePaymentFields() {
        const paymentType = document.getElementById('payment_type').value;
        const installmentFields = document.querySelectorAll('.installment-field');
        const fullPaymentFields = document.querySelectorAll('.full-payment-field');

        if (paymentType === 'installments') {
            installmentFields.forEach(field => field.style.display = 'block');
            fullPaymentFields.forEach(field => field.style.display = 'none');
        } else {
            installmentFields.forEach(field => field.style.display = 'none');
            fullPaymentFields.forEach(field => field.style.display = 'block');
        }
    }

    function calculateFirstPaymentAmount() {
        const pvEl = document.getElementById('project_value');
        const pctEl = document.getElementById('first_payment_percentage');
        const amtEl = document.getElementById('first_payment_amount');
        if (!pvEl || !pctEl || !amtEl) {
            return;
        }
        const projectValue = parseFloat(pvEl.value) || 0;
        const percentage = parseFloat(pctEl.value) || 0;
        const amount = (projectValue * percentage) / 100;

        amtEl.value = amount.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', function () {
        togglePaymentFields();
    });
</script>

@endsection
