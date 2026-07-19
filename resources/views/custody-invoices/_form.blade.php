@php
    $isEdit = !empty($editingInvoice);
    $d = $isEdit ? ($editingInvoice->invoice_date ?? now()) : now();
    $dayVal = old('invoice_day', $isEdit ? $d->day : now()->day);
    $monthVal = old('invoice_month', $isEdit ? $d->month : now()->month);
    $yearVal = old('invoice_year', $isEdit ? $d->year : now()->year);
    $maxTotal = $isEdit ? $editMaxTotal : $availableToRegister;
    $formId = $isEdit ? 'invoice-edit-form' : 'invoice-form';
    $amountVal = old('amount', $isEdit ? number_format((float) $editingInvoice->amount, 2, '.', '') : '');
    $taxVal = $amountVal !== '' ? number_format(\App\Services\CustodyInvoiceService::calculateVat((float) $amountVal), 2, '.', '') : '0.00';
@endphp

<div class="ci-form-card">
  <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:16px;">
    <h2 style="margin:0;">{{ $isEdit ? __('custody_invoice.edit_title') : __('custody_invoice.add_title') }}</h2>
    @if($isEdit)
      <a href="{{ route('custody-invoices.index') }}" class="btn btn-secondary btn-sm">{{ __('custody_invoice.cancel_edit') }}</a>
    @endif
  </div>

  <form method="post"
        action="{{ $isEdit ? route('custody-invoices.update', $editingInvoice) : route('custody-invoices.store') }}"
        enctype="multipart/form-data"
        class="form-grid ci-invoice-form"
        id="{{ $formId }}"
        data-max-total="{{ number_format($maxTotal, 2, '.', '') }}"
        data-vat-rate="{{ \App\Services\CustodyInvoiceService::VAT_RATE }}">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="form-group @if($errors->hasAny(['invoice_day', 'invoice_month', 'invoice_year'])) has-error @endif">
      <label>{{ __('custody_invoice.field_date') }}</label>
      <div class="ci-date-row">
        <div class="ci-date-field">
          <label for="{{ $formId }}_day">{{ __('custody_invoice.date_day') }}</label>
          <input type="number" id="{{ $formId }}_day" name="invoice_day" min="1" max="31" value="{{ $dayVal }}" required>
        </div>
        <span class="ci-date-sep">/</span>
        <div class="ci-date-field">
          <label for="{{ $formId }}_month">{{ __('custody_invoice.date_month') }}</label>
          <input type="number" id="{{ $formId }}_month" name="invoice_month" min="1" max="12" value="{{ $monthVal }}" required>
        </div>
        <span class="ci-date-sep">/</span>
        <div class="ci-date-field">
          <label for="{{ $formId }}_year">{{ __('custody_invoice.date_year') }}</label>
          <input type="number" id="{{ $formId }}_year" name="invoice_year" min="2000" max="{{ now()->year }}" value="{{ $yearVal }}" required>
        </div>
      </div>
      @error('invoice_day')<span class="field-error">{{ $message }}</span>@enderror
      @error('invoice_month')<span class="field-error">{{ $message }}</span>@enderror
      @error('invoice_year')<span class="field-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-group @error('supplier_name') has-error @enderror">
      <label for="{{ $formId }}_supplier">{{ __('custody_invoice.field_supplier') }}</label>
      <input type="text" id="{{ $formId }}_supplier" name="supplier_name" value="{{ old('supplier_name', $isEdit ? $editingInvoice->supplier_name : '') }}" required maxlength="255" autocomplete="organization">
      @error('supplier_name')<span class="field-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-group @error('supplier_tax_number') has-error @enderror">
      <label for="{{ $formId }}_tax_no">{{ __('custody_invoice.field_tax_number') }}</label>
      <input type="text" id="{{ $formId }}_tax_no" name="supplier_tax_number" value="{{ old('supplier_tax_number', $isEdit ? $editingInvoice->supplier_tax_number : '') }}" maxlength="15" inputmode="numeric" pattern="[0-9]{15}" placeholder="3XXXXXXXXXXXXX3" dir="ltr" style="text-align:left;">
      @error('supplier_tax_number')<span class="field-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-group @error('description') has-error @enderror" style="grid-column:1/-1;">
      <label for="{{ $formId }}_desc">{{ __('custody_invoice.field_description') }}</label>
      <textarea id="{{ $formId }}_desc" name="description" rows="3" maxlength="2000">{{ old('description', $isEdit ? $editingInvoice->description : '') }}</textarea>
      @error('description')<span class="field-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-group @error('amount') has-error @enderror">
      <label for="{{ $formId }}_amount">{{ __('custody_invoice.field_amount') }}</label>
      <input type="number" id="{{ $formId }}_amount" name="amount" step="0.01" min="0.01" value="{{ $amountVal }}" class="ci-line-amount" required>
      @error('amount')<span class="field-error">{{ $message }}</span>@enderror
    </div>

    <div class="form-group">
      <label for="{{ $formId }}_tax">{{ __('custody_invoice.field_tax') }} (15%)</label>
      <input type="text" id="{{ $formId }}_tax" value="{{ $taxVal }}" class="ci-line-tax" readonly tabindex="-1" style="background:#f3f4f6; cursor:default;">
      <span class="ci-field-hint">{{ __('custody_invoice.tax_auto_hint') }}</span>
    </div>

    <div class="form-group">
      <label>{{ __('custody_invoice.field_total') }}</label>
      <div class="ci-total-display ci-line-total" style="font-size:18px; font-weight:800; padding:8px 0;">0.00</div>
      <span class="ci-field-hint">{{ __('custody_invoice.amount_hint', ['max' => number_format($maxTotal, 2)]) }}</span>
    </div>

    <div class="form-group @error('attachment') has-error @enderror" style="grid-column:1/-1;">
      <label for="{{ $formId }}_file">{{ $isEdit ? __('custody_invoice.replace_attachment') : __('custody_invoice.field_attachment') }}</label>
      <div class="ci-file-wrap">
        @if($isEdit && $editingInvoice->hasAttachment())
          <div style="margin-bottom:8px;">
            <span class="ci-field-hint">{{ __('custody_invoice.current_attachment') }}</span>
            <a href="{{ route('custody-invoices.attachment', $editingInvoice) }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm">{{ __('custody_invoice.open_file') }}</a>
          </div>
        @endif
        <input type="file" id="{{ $formId }}_file" name="attachment" accept="image/jpeg,image/png,image/webp,image/gif,application/pdf">
        <span class="ci-field-hint">{{ __('custody_invoice.attachment_hint') }}</span>
        <div class="ci-file-name ci-file-preview" hidden></div>
      </div>
      @error('attachment')<span class="field-error">{{ $message }}</span>@enderror
    </div>

    @error('invoice')<div class="field-error" style="grid-column:1/-1;">{{ $message }}</div>@enderror

    <div style="grid-column:1/-1;">
      <button type="submit" class="btn btn-primary">{{ $isEdit ? __('custody_invoice.update_btn') : __('custody_invoice.submit') }}</button>
    </div>
  </form>
</div>
