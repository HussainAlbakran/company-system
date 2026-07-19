@php
    use App\Support\ContractPaymentTypes;
    $paymentType = old('payment_type', $selectedPaymentType ?? ContractPaymentTypes::FULL);
    $finFull = $finFull ?? auth()->user()->canViewProjectFinancials();
@endphp

<div class="col-md-12 mb-3">
    <label>{{ __('contracts.field_payment_type') }}</label>
    <select name="payment_type" id="payment_type" class="form-control" onchange="toggleContractPaymentFields()" required>
        <option value="full" @selected($paymentType === 'full')>{{ __('contracts.payment_full') }}</option>
        <option value="installments_2" @selected(in_array($paymentType, ['installments_2', 'installments'], true))>{{ __('contracts.payment_installments_2') }}</option>
        <option value="installments_3" @selected($paymentType === 'installments_3')>{{ __('contracts.payment_installments_3') }}</option>
        <option value="installments_4" @selected($paymentType === 'installments_4')>{{ __('contracts.payment_installments_4') }}</option>
        <option value="government" @selected($paymentType === 'government')>{{ __('contracts.payment_government') }}</option>
    </select>
</div>

@if($finFull)
<div id="government-payment-note" class="col-md-12 mb-3" style="display:none;">
    <div class="alert-success" style="margin:0;">
        {{ __('contracts.government_payment_note') }}
    </div>
</div>

<div id="installment-plan-hint" class="col-md-12 mb-3" style="display:none;">
    <p class="page-subtitle" style="margin:0;" id="installment_plan_hint_text"></p>
</div>

<div class="col-md-6 mb-3 full-payment-field">
    <label>{{ __('contracts.field_full_payment_amount') }}</label>
    <input type="number" step="0.01" name="full_payment_amount" class="form-control" value="{{ old('full_payment_amount', $fullPaymentAmount ?? '') }}" placeholder="{{ __('contracts.placeholder_amount_paid') }}">
</div>

<div class="col-md-6 mb-3 installment-field" style="display:none;">
    <label>{{ __('contracts.field_first_payment_title') }}</label>
    <input type="text" name="first_payment_title" class="form-control" value="{{ old('first_payment_title', $firstPaymentTitle ?? '') }}" placeholder="{{ __('contracts.placeholder_first_payment') }}">
</div>

<div class="col-md-6 mb-3 installment-field" style="display:none;">
    <label>{{ __('contracts.field_first_payment_pct') }}</label>
    <input type="number" step="0.01" name="first_payment_percentage" id="first_payment_percentage" class="form-control" value="{{ old('first_payment_percentage', $firstPaymentPercentage ?? '') }}" placeholder="{{ __('contracts.placeholder_pct') }}" oninput="calculateFirstPaymentAmount()">
</div>

<div class="col-md-6 mb-3 installment-field" style="display:none;">
    <label>{{ __('contracts.field_first_payment_amount') }}</label>
    <input type="number" step="0.01" name="first_payment_amount" id="first_payment_amount" class="form-control" value="{{ old('first_payment_amount', $firstPaymentAmount ?? '') }}" placeholder="{{ __('contracts.placeholder_auto_calc') }}">
</div>

<div class="col-md-6 mb-3 installment-field" style="display:none;">
    <label>{{ __('contracts.field_first_payment_due') }}</label>
    <input type="date" name="first_payment_due_date" class="form-control" value="{{ old('first_payment_due_date', $firstPaymentDueDate ?? '') }}">
</div>
@else
<input type="hidden" name="full_payment_amount" value="0">
<input type="hidden" name="first_payment_title" value="">
<input type="hidden" name="first_payment_percentage" value="0">
<input type="hidden" name="first_payment_amount" value="0">
<input type="hidden" name="first_payment_due_date" value="">
@endif

<script>
    const contractPaymentI18n = {
        installmentHint: @json(__('contracts.installment_plan_hint')),
    };

    function toggleContractPaymentFields() {
        const paymentType = document.getElementById('payment_type')?.value;
        const installmentFields = document.querySelectorAll('.installment-field');
        const fullPaymentFields = document.querySelectorAll('.full-payment-field');
        const governmentNote = document.getElementById('government-payment-note');
        const installmentHint = document.getElementById('installment-plan-hint');
        const hintText = document.getElementById('installment_plan_hint_text');
        const projectValue = parseFloat(document.getElementById('project_value')?.value) || 0;

        const isInstallment = ['installments_2', 'installments_3', 'installments_4', 'installments'].includes(paymentType);
        const isGovernment = paymentType === 'government';
        const isFull = paymentType === 'full';

        installmentFields.forEach(el => el.style.display = isInstallment ? 'block' : 'none');
        fullPaymentFields.forEach(el => el.style.display = isFull ? 'block' : 'none');
        if (governmentNote) governmentNote.style.display = isGovernment ? 'block' : 'none';
        if (installmentHint) installmentHint.style.display = isInstallment ? 'block' : 'none';

        if (hintText && isInstallment) {
            let count = 2;
            if (paymentType === 'installments_3') count = 3;
            if (paymentType === 'installments_4') count = 4;
            const share = count > 0 ? (projectValue / count).toFixed(2) : '0.00';
            hintText.textContent = contractPaymentI18n.installmentHint
                .replace(':count', count)
                .replace(':share', share);
        }
    }

    function calculateFirstPaymentAmount() {
        const pvEl = document.getElementById('project_value');
        const pctEl = document.getElementById('first_payment_percentage');
        const amtEl = document.getElementById('first_payment_amount');
        if (!pvEl || !pctEl || !amtEl) return;
        const projectValue = parseFloat(pvEl.value) || 0;
        const percentage = parseFloat(pctEl.value) || 0;
        amtEl.value = ((projectValue * percentage) / 100).toFixed(2);
        toggleContractPaymentFields();
    }

    document.addEventListener('DOMContentLoaded', function () {
        toggleContractPaymentFields();
        document.getElementById('project_value')?.addEventListener('input', function () {
            calculateFirstPaymentAmount();
            toggleContractPaymentFields();
        });
    });
</script>
