@extends('layouts.app')

@section('page_title', $settlement->displayTitle())
@section('page_subtitle', $settlement->employee->name ?? '')

@section('content')
@php
    $canEdit = $settlement->isDraft();
    $rows = $settlement->invoices->sortBy('line_number')->values();
    $settlementDate = $settlement->settlement_date ?? now();
@endphp

<style>
    .settlement-shell { max-width: 1200px; margin: 0 auto; }
    .settlement-actions { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:16px; }
    .settlement-print-area { background:#fff; color:#000; border:1px solid #cbd5e1; padding:16px; }
    .settlement-top { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; margin-bottom:12px; }
    .settlement-logo-box { width:200px; }
    .settlement-logo-box img { max-height:72px; width:auto; }
    .settlement-logo-text { font-size:11px; line-height:1.4; margin-top:6px; color:#1e3a5f; }
    .settlement-meta { min-width:260px; border:1px solid #94a3b8; }
    .settlement-meta-row { display:flex; border-bottom:1px solid #94a3b8; }
    .settlement-meta-row:last-child { border-bottom:0; }
    .settlement-meta-label { background:#dbeafe; padding:8px 12px; font-weight:700; min-width:110px; border-left:1px solid #94a3b8; }
    .settlement-meta-value { padding:8px 12px; flex:1; font-weight:700; }
    .settlement-table { width:100%; border-collapse:collapse; font-size:13px; }
    .settlement-table th, .settlement-table td { border:1px solid #1e3a5f; padding:6px 4px; text-align:center; vertical-align:middle; }
    .settlement-table thead th { background:#1e4a8a; color:#fff; font-weight:700; }
    .settlement-table .col-class { background:#dbeafe; }
    .settlement-table tfoot td { background:#1e4a8a; color:#fff; font-weight:800; }
    .settlement-input { width:100%; min-width:48px; border:1px solid #cbd5e1; padding:4px; font-size:12px; text-align:center; box-sizing:border-box; }
    .settlement-input-wide { text-align:right; }
    .settlement-date-parts { display:flex; align-items:center; gap:2px; justify-content:center; direction:ltr; }
    .settlement-date-parts input { width:42px; padding:3px; font-size:11px; text-align:center; }
    .settlement-signatures { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:20px; }
    .settlement-sign-box { border:1px solid #94a3b8; background:#dbeafe; padding:24px 12px; text-align:center; font-weight:700; min-height:70px; }
    @media print {
        .no-print { display:none !important; }
        .settlement-input { border:0; background:transparent; }
        .settlement-print-area { border:0; padding:0; }
        .company-print-logo { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
        .settlement-table thead th, .settlement-table tfoot td, .settlement-table .col-class, .settlement-meta-label, .settlement-sign-box { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    }
</style>

<div class="settlement-shell">
    @if(session('success'))<div class="alert-success no-print">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-danger no-print">{{ session('error') }}</div>@endif

    <div class="settlement-actions no-print">
        <a href="{{ route('custody-settlements.index') }}" class="btn btn-secondary btn-sm">{{ __('custody_settlement.back_list') }}</a>
        @if($canEdit)
            <button type="submit" form="settlement-form" class="btn btn-warning btn-sm">{{ __('custody_settlement.save_btn') }}</button>
            <form method="post" action="{{ route('custody-settlements.approve', $settlement) }}" style="display:inline;" onsubmit="return confirm(@json(__('custody_settlement.confirm_approve')))">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">{{ __('custody_settlement.approve_btn') }}</button>
            </form>
        @endif
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">{{ __('custody_settlement.print_btn') }}</button>
    </div>

    <form method="post" action="{{ route('custody-settlements.update', $settlement) }}" id="settlement-form" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="settlement-print-area print-area">
            <div class="settlement-top">
                <div class="settlement-logo-box">
                    <x-company-print-logo />
                    <div class="settlement-logo-text">
                        <div>شركة التقدم للخرسانة الجاهزة</div>
                        <div>ADVANCE PRECAST COMPANY</div>
                    </div>
                </div>
                <div class="settlement-meta">
                    <div class="settlement-meta-row">
                        <div class="settlement-meta-label">{{ __('custody_settlement.sheet_title') }}</div>
                        <div class="settlement-meta-value">
                            @if($settlement->referenceCode())
                                {{ $settlement->referenceCode() }}
                            @else
                                {{ __('custody_settlement.draft_label') }}
                            @endif
                        </div>
                    </div>
                    <div class="settlement-meta-row">
                        <div class="settlement-meta-label">{{ __('custody_settlement.field_date') }}</div>
                        <div class="settlement-meta-value">
                            @if($canEdit)
                                <input type="date" name="settlement_date" value="{{ $settlementDate->format('Y-m-d') }}" class="settlement-input no-print" style="max-width:160px;">
                                <span class="print-only">{{ $settlementDate->format('Y-m-d') }}</span>
                            @else
                                {{ $settlement->settlement_date?->format('Y-m-d') }}
                                @if($settlement->approved_at)
                                    <div style="font-size:11px; font-weight:400;">{{ __('custody_settlement.approved_at') }}: {{ $settlement->approved_at->format('Y-m-d') }}</div>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="settlement-meta-row">
                        <div class="settlement-meta-label">{{ __('custody_settlement.employee') }}</div>
                        <div class="settlement-meta-value">{{ $settlement->employee->name ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <table class="settlement-table">
                <thead>
                    <tr>
                        <th style="width:36px;">م</th>
                        <th style="width:100px;">{{ __('custody_settlement.col_date') }}</th>
                        <th>{{ __('custody_settlement.col_supplier') }}</th>
                        <th style="width:110px;">{{ __('custody_settlement.col_tax_no') }}</th>
                        <th style="width:90px;">{{ __('custody_settlement.col_class') }}</th>
                        <th>{{ __('custody_settlement.col_description') }}</th>
                        <th style="width:80px;">{{ __('custody_settlement.col_amount') }}</th>
                        <th style="width:70px;">{{ __('custody_settlement.col_tax') }}</th>
                        <th style="width:90px;">{{ __('custody_settlement.col_total') }}</th>
                        <th class="no-print" style="width:100px;">{{ __('custody_settlement.col_file') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $index => $invoice)
                        @php
                            $line = $index + 1;
                            $d = $invoice->invoice_date ?? $settlementDate;
                        @endphp
                        <tr>
                            <td>{{ $line }}</td>
                            <td>
                                @if($canEdit)
                                    <input type="hidden" name="lines[{{ $index }}][id]" value="{{ $invoice->id }}">
                                    <div class="settlement-date-parts no-print">
                                        <input type="number" name="lines[{{ $index }}][invoice_day]" min="1" max="31" value="{{ $d->day }}">
                                        <span>/</span>
                                        <input type="number" name="lines[{{ $index }}][invoice_month]" min="1" max="12" value="{{ $d->month }}">
                                        <span>/</span>
                                        <input type="number" name="lines[{{ $index }}][invoice_year]" min="2000" max="2100" value="{{ $d->year }}">
                                    </div>
                                    <span class="print-only">{{ $d->format('Y-m-d') }}</span>
                                @else
                                    {{ $invoice->invoice_date?->format('Y-m-d') ?? '-' }}
                                @endif
                            </td>
                            <td>
                                @if($canEdit)
                                    <input type="text" name="lines[{{ $index }}][supplier_name]" value="{{ $invoice->supplier_name }}" class="settlement-input settlement-input-wide">
                                @else
                                    {{ $invoice->supplier_name }}
                                @endif
                            </td>
                            <td>
                                @if($canEdit)
                                    <input type="text" name="lines[{{ $index }}][supplier_tax_number]" value="{{ $invoice->supplier_tax_number }}" class="settlement-input">
                                @else
                                    {{ $invoice->supplier_tax_number ?? '' }}
                                @endif
                            </td>
                            <td class="col-class">
                                @if($canEdit)
                                    <input type="text" name="lines[{{ $index }}][classification]" value="{{ $invoice->classification }}" class="settlement-input">
                                @else
                                    {{ $invoice->classification ?? '' }}
                                @endif
                            </td>
                            <td>
                                @if($canEdit)
                                    <input type="text" name="lines[{{ $index }}][description]" value="{{ $invoice->description }}" class="settlement-input settlement-input-wide">
                                @else
                                    {{ $invoice->description ?? '' }}
                                @endif
                            </td>
                            <td>
                                @if($canEdit)
                                    <input type="number" step="0.01" min="0" name="lines[{{ $index }}][amount]" value="{{ number_format((float) $invoice->amount, 2, '.', '') }}" class="settlement-input line-amount" data-row="{{ $index }}">
                                @else
                                    {{ number_format((float) $invoice->amount, 2) }}
                                @endif
                            </td>
                            <td>
                                @if($canEdit)
                                    <input type="number" step="0.01" min="0" name="lines[{{ $index }}][tax_amount]" value="{{ number_format((float) $invoice->tax_amount, 2, '.', '') }}" class="settlement-input line-tax" data-row="{{ $index }}">
                                @else
                                    {{ number_format((float) $invoice->tax_amount, 2) }}
                                @endif
                            </td>
                            <td class="line-total" data-row="{{ $index }}">
                                <span class="line-total-value">{{ number_format((float) $invoice->total_amount, 2) }}</span>
                            </td>
                            <td class="no-print">
                                @if($invoice->hasAttachment())
                                    <a href="{{ route('custody-settlements.attachment', $invoice) }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm" style="margin-bottom:4px;">{{ __('custody_settlement.open_file') }}</a>
                                @endif
                                @if($canEdit)
                                    <input type="file" name="line_attachment_{{ $invoice->id }}" accept="image/jpeg,image/png,image/webp,image/gif,application/pdf" class="settlement-input">
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align:center; color:#6b7280; padding:20px;">{{ __('custody_settlement.no_invoice_lines') }}</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" style="text-align:center;">{{ __('custody_settlement.grand_total_label') }}</td>
                        <td id="sum-amount">{{ number_format((float)$settlement->total_amount, 2) }}</td>
                        <td id="sum-tax">{{ number_format((float)$settlement->total_tax, 2) }}</td>
                        <td id="sum-total">{{ number_format((float)$settlement->grand_total, 2) }}</td>
                        <td class="no-print"></td>
                    </tr>
                </tfoot>
            </table>

            <div class="settlement-signatures">
                <div class="settlement-sign-box">{{ __('custody_settlement.sign_accounts') }}</div>
                <div class="settlement-sign-box">{{ __('custody_settlement.sign_approve') }}</div>
            </div>
        </div>
    </form>
</div>

@if($canEdit)
<script>
(function () {
    function recalc() {
        let sumA = 0, sumT = 0, sumG = 0;
        document.querySelectorAll('.line-amount').forEach(function (el) {
            const row = el.dataset.row;
            const amount = parseFloat(el.value || '0') || 0;
            const taxEl = document.querySelector('.line-tax[data-row="' + row + '"]');
            const tax = parseFloat(taxEl?.value || '0') || 0;
            const total = Math.round((amount + tax) * 100) / 100;
            const valEl = document.querySelector('.line-total[data-row="' + row + '"] .line-total-value');
            if (valEl) valEl.textContent = total.toFixed(2);
            sumA += amount; sumT += tax; sumG += total;
        });
        document.getElementById('sum-amount').textContent = sumA.toFixed(2);
        document.getElementById('sum-tax').textContent = sumT.toFixed(2);
        document.getElementById('sum-total').textContent = sumG.toFixed(2);
    }
    document.querySelectorAll('.line-amount, .line-tax').forEach(function (el) {
        el.addEventListener('input', recalc);
    });
})();
</script>
@endif

<style>
.print-only { display:none; }
@media print {
    .print-only { display:inline !important; }
    html, body { background:#fff !important; }
    body * { visibility:hidden !important; }
    .print-area, .print-area * { visibility:visible !important; }
    .print-area { position:fixed; inset:0; padding:12mm; background:#fff; }
}
</style>
@endsection
