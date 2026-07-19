@extends('layouts.app')

@section('page_title', __('custody_invoice.page_title'))
@section('page_subtitle', __('custody_invoice.page_subtitle'))

@section('content')
@php
    $registeredTotal = $custody ? (float) $invoices->sum('total_amount') : 0;
    $editingInvoice = $editingInvoice ?? null;
    $editMaxTotal = $editMaxTotal ?? 0;
@endphp

<style>
    .ci-page .ci-kpi-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        margin-bottom: 20px;
    }
    .ci-page .ci-kpi {
        background: #fff;
        border: 1px solid #000;
        border-radius: 10px;
        padding: 14px 16px;
    }
    .ci-page .ci-kpi-label {
        font-size: 11px;
        font-weight: 700;
        color: #4b5563;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .ci-page .ci-kpi-value {
        margin-top: 8px;
        font-size: 20px;
        font-weight: 800;
        line-height: 1.2;
        color: #111827;
    }
    .ci-page .ci-kpi-remaining .ci-kpi-value { color: #15803d; }
    .ci-page .ci-kpi-pending .ci-kpi-value { color: #ea580c; }
    .ci-page .ci-kpi-available .ci-kpi-value { color: #1d4ed8; }

    .ci-page .ci-form-card {
        background: #fff;
        border: 1px solid #000;
        border-radius: 10px;
        padding: 18px;
        margin-bottom: 22px;
    }
    .ci-page .ci-form-card h2 {
        margin: 0 0 16px;
        font-size: 17px;
        color: #111827;
    }
    .ci-page .ci-date-row {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        direction: ltr;
        justify-content: flex-end;
        flex-wrap: wrap;
    }
    .ci-page .ci-date-field {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }
    .ci-page .ci-date-field label {
        font-size: 11px;
        color: #6b7280;
        font-weight: 600;
    }
    .ci-page .ci-date-field input {
        width: 64px;
        text-align: center;
        padding: 8px 6px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 15px;
    }
    .ci-page .ci-date-sep {
        padding-bottom: 10px;
        color: #9ca3af;
        font-weight: 700;
    }
    .ci-page .ci-field-hint {
        display: block;
        margin-top: 6px;
        font-size: 12px;
        color: #6b7280;
    }
    .ci-page .ci-file-wrap {
        border: 1px dashed #94a3b8;
        border-radius: 10px;
        padding: 14px;
        background: #f8fafc;
    }
    .ci-page .ci-file-name {
        margin-top: 8px;
        font-size: 13px;
        color: #1d4ed8;
        word-break: break-all;
    }
    .ci-page .ci-empty-state {
        text-align: center;
        padding: 40px 20px;
        background: #f9fafb;
        border: 1px dashed #d1d5db;
        border-radius: 10px;
        color: #6b7280;
    }
    .ci-page .ci-list-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 12px;
    }
    .ci-page .ci-list-header h2 {
        margin: 0;
        font-size: 17px;
    }
    .ci-page .ci-list-total {
        font-size: 14px;
        color: #374151;
        background: #f3f4f6;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 700;
    }
    .ci-page .field-error {
        color: #dc2626;
        font-size: 12px;
        margin-top: 4px;
    }
    .ci-page .form-group.has-error input,
    .ci-page .form-group.has-error textarea {
        border-color: #dc2626;
    }
</style>

<div class="page-card ci-page">
    <div class="page-header" style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px;">
        <div>
            <h1 class="page-title">{{ __('custody_invoice.page_title') }}</h1>
            <p style="margin:6px 0 0; color:#6b7280;">{{ __('custody_invoice.page_subtitle') }}</p>
        </div>
    </div>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-danger">{{ session('error') }}</div>@endif
    @if($errors->has('custody') || $errors->has('employee'))
        <div class="alert-danger">{{ $errors->first('custody') ?: $errors->first('employee') }}</div>
    @endif

    @if(!$custody)
        <div class="ci-empty-state">
            <p style="margin:0 0 8px; font-size:16px; font-weight:700; color:#374151;">{{ __('custody_invoice.no_open_custody') }}</p>
            <p style="margin:0;">{{ __('custody_invoice.no_custody_hint') }}</p>
        </div>
    @else
        <div class="ci-kpi-grid">
            <div class="ci-kpi">
                <div class="ci-kpi-label">{{ __('custody_invoice.employee') }}</div>
                <div class="ci-kpi-value" style="font-size:16px;">{{ $employee->name }}</div>
            </div>
            <div class="ci-kpi">
                <div class="ci-kpi-label">{{ __('financial_custody.amount_issued') }}</div>
                <div class="ci-kpi-value">{{ number_format((float) $custody->amount_issued, 2) }}</div>
            </div>
            @if((float) $custody->carried_over_amount > 0)
            <div class="ci-kpi">
                <div class="ci-kpi-label">{{ __('financial_custody.carried_over_amount') }}</div>
                <div class="ci-kpi-value">{{ number_format((float) $custody->carried_over_amount, 2) }}</div>
            </div>
            <div class="ci-kpi">
                <div class="ci-kpi-label">{{ __('financial_custody.new_cash_amount') }}</div>
                <div class="ci-kpi-value">{{ number_format($custody->newCashAmount(), 2) }}</div>
            </div>
            @endif
            <div class="ci-kpi ci-kpi-remaining">
                <div class="ci-kpi-label">{{ __('financial_custody.amount_remaining') }}</div>
                <div class="ci-kpi-value">{{ number_format((float) $custody->amount_remaining, 2) }}</div>
            </div>
            <div class="ci-kpi ci-kpi-pending">
                <div class="ci-kpi-label">{{ __('custody_invoice.pending_invoices') }}</div>
                <div class="ci-kpi-value">{{ number_format($pendingTotal, 2) }}</div>
            </div>
            <div class="ci-kpi ci-kpi-available">
                <div class="ci-kpi-label">{{ __('custody_invoice.available_to_register') }}</div>
                <div class="ci-kpi-value">{{ number_format($availableToRegister, 2) }}</div>
            </div>
            <div class="ci-kpi">
                <div class="ci-kpi-label">{{ __('custody_invoice.issued_at') }}</div>
                <div class="ci-kpi-value" style="font-size:16px;">{{ $custody->issued_at?->format('Y-m-d') ?? '-' }}</div>
            </div>
        </div>

        @if($editingInvoice)
            @include('custody-invoices._form', ['editingInvoice' => $editingInvoice, 'availableToRegister' => $availableToRegister, 'editMaxTotal' => $editMaxTotal])
        @elseif($availableToRegister > 0)
            @include('custody-invoices._form', ['editingInvoice' => null, 'availableToRegister' => $availableToRegister, 'editMaxTotal' => 0])
        @else
            <div class="alert-danger" style="margin-bottom:20px;">
                {{ __('custody_invoice.no_available_balance') }}
            </div>
        @endif

        <div class="ci-list-header">
            <h2>{{ __('custody_invoice.list_title') }}</h2>
            @if($invoices->isNotEmpty())
                <span class="ci-list-total">
                    {{ __('custody_invoice.list_total') }}: {{ number_format($registeredTotal, 2) }}
                    ({{ $invoices->count() }} {{ __('custody_invoice.invoices_count') }})
                </span>
            @endif
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('custody_invoice.field_date') }}</th>
                        <th>{{ __('custody_invoice.field_supplier') }}</th>
                        <th>{{ __('custody_invoice.field_tax_number') }}</th>
                        <th>{{ __('custody_invoice.field_description') }}</th>
                        <th>{{ __('custody_invoice.field_amount') }}</th>
                        <th>{{ __('custody_invoice.field_tax') }}</th>
                        <th>{{ __('custody_invoice.field_total') }}</th>
                        <th>{{ __('custody_invoice.field_attachment') }}</th>
                        <th>{{ __('custody_invoice.th_status') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_date?->format('Y-m-d') }}</td>
                            <td>{{ $invoice->supplier_name }}</td>
                            <td dir="ltr">{{ $invoice->supplier_tax_number ?: '—' }}</td>
                            <td>{{ $invoice->description ?: '—' }}</td>
                            <td>{{ number_format((float) $invoice->amount, 2) }}</td>
                            <td>{{ number_format((float) $invoice->tax_amount, 2) }}</td>
                            <td style="font-weight:700;">{{ number_format((float) $invoice->total_amount, 2) }}</td>
                            <td>
                                @if($invoice->hasAttachment())
                                    <a href="{{ route('custody-invoices.attachment', $invoice) }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm">{{ __('custody_invoice.open_file') }}</a>
                                @else
                                    <span style="color:#9ca3af;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($invoice->status === 'approved')
                                    <span class="badge badge-green">{{ __('custody_invoice.status_approved') }}</span>
                                @elseif($invoice->status === 'on_settlement')
                                    <span class="badge badge-orange">{{ __('custody_invoice.status_on_settlement') }}</span>
                                @else
                                    <span class="badge badge-gray">{{ __('custody_invoice.status_registered') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($invoice->status === \App\Models\FinancialCustodyInvoice::STATUS_REGISTERED && $invoice->financial_custody_settlement_id === null)
                                    <a href="{{ route('custody-invoices.index', ['edit' => $invoice->id]) }}" class="btn btn-warning btn-sm">{{ __('custody_invoice.edit_btn') }}</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; color:#6b7280; padding:24px;">{{ __('custody_invoice.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>

@if($custody)
<script>
(function () {
    var selectedLabel = @json(__('custody_invoice.attachment_selected'));

    function bindForm(form) {
        if (!form) return;

        var amountEl = form.querySelector('.ci-line-amount');
        var taxEl = form.querySelector('.ci-line-tax');
        var totalEl = form.querySelector('.ci-line-total');
        var fileInput = form.querySelector('input[type="file"]');
        var filePreview = form.querySelector('.ci-file-preview');
        var maxTotal = parseFloat(form.dataset.maxTotal || '0') || 0;
        var vatRate = parseFloat(form.dataset.vatRate || '0.15') || 0.15;

        function recalc() {
            var amount = parseFloat(amountEl?.value || '0') || 0;
            var tax = Math.round(amount * vatRate * 100) / 100;
            var total = Math.round((amount + tax) * 100) / 100;
            if (taxEl) taxEl.value = tax.toFixed(2);
            if (totalEl) totalEl.textContent = total.toFixed(2);
            if (amountEl) {
                amountEl.style.borderColor = maxTotal > 0 && total > maxTotal ? '#dc2626' : '';
            }
        }

        amountEl?.addEventListener('input', recalc);
        recalc();

        fileInput?.addEventListener('change', function () {
            if (!filePreview) return;
            if (fileInput.files && fileInput.files[0]) {
                filePreview.textContent = selectedLabel + ' ' + fileInput.files[0].name;
                filePreview.hidden = false;
            } else {
                filePreview.hidden = true;
                filePreview.textContent = '';
            }
        });
    }

    document.querySelectorAll('.ci-invoice-form').forEach(bindForm);
})();
</script>
@endif
@endsection
