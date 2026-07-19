@extends('layouts.app')

@section('page_title', __('employee_advance.issue_title'))
@section('page_subtitle', __('employee_advance.issue_subtitle'))

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between;">
        <h1 class="page-title">{{ __('employee_advance.issue_title') }}</h1>
        <a href="{{ route('employee-advances.index') }}" class="btn btn-secondary btn-sm">{{ __('employee_advance.back') }}</a>
    </div>

    <p class="panel-subtitle" style="margin-bottom:12px;">{{ __('employee_advance.calc_hint') }}</p>

    @if($errors->any())
        <div class="alert-danger"><ul style="margin:0;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="post" action="{{ route('employee-advances.store') }}" class="form-grid" style="margin-top:16px;" id="advanceForm">
        @csrf
        <div class="form-group">
            <label>{{ __('employee_advance.employee') }}</label>
            <select name="employee_id" id="advance_employee" required>
                <option value="">—</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" data-salary="{{ (float)($emp->salary ?? 0) }}" @selected(old('employee_id')==$emp->id)>{{ $emp->name }} ({{ __('profile.base_salary') }}: {{ number_format((float)($emp->salary ?? 0), 2) }})</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>{{ __('employee_advance.total_amount') }}</label>
            <input type="number" step="0.01" min="0.01" name="total_amount" id="advance_total" value="{{ old('total_amount') }}" required>
        </div>
        <div class="form-group">
            <label>{{ __('employee_advance.installment_count') }}</label>
            <select name="installment_count" id="advance_count" required>
                @foreach([2,3,4,5,6] as $n)
                    <option value="{{ $n }}" @selected((int)old('installment_count')===$n)>{{ __('employee_advance.installment_option', ['count'=>$n]) }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="grid-column:1/-1;">
            <div id="advance_preview" class="detail-box" style="padding:12px; background:#f8fafc; display:none;">
                <strong>{{ __('employee_advance.preview_title') }}</strong>
                <div id="advance_preview_text" style="margin-top:8px;"></div>
            </div>
        </div>
        <div class="form-group">
            <label>{{ __('employee_advance.issued_at') }}</label>
            <input type="date" name="issued_at" id="advance_issued_at" value="{{ old('issued_at', now()->format('Y-m-d')) }}">
        </div>
        <div class="form-group">
            <label>{{ __('employee_advance.repayment_delay') }}</label>
            <select name="repayment_delay_months" id="advance_delay" required>
                @foreach(range(0, 12) as $delay)
                    <option value="{{ $delay }}" @selected((int) old('repayment_delay_months', 0) === $delay)>
                        @if($delay === 0)
                            {{ __('employee_advance.repayment_delay_option_zero') }}
                        @else
                            {{ __('employee_advance.repayment_delay_option', ['months' => $delay]) }}
                        @endif
                    </option>
                @endforeach
            </select>
            <small style="color:#6b7280; display:block; margin-top:4px;">{{ __('employee_advance.repayment_delay_hint') }}</small>
        </div>
        <div class="form-group">
            <label>{{ __('employee_advance.notes') }}</label>
            <textarea name="notes" rows="3">{{ old('notes') }}</textarea>
        </div>
        <div style="grid-column:1/-1;">
            <button type="submit" class="btn btn-primary">{{ __('employee_advance.submit') }}</button>
        </div>
    </form>
</div>
<script>
(function () {
    const emp = document.getElementById('advance_employee');
    const total = document.getElementById('advance_total');
    const count = document.getElementById('advance_count');
    const issuedAt = document.getElementById('advance_issued_at');
    const delay = document.getElementById('advance_delay');
    const preview = document.getElementById('advance_preview');
    const previewText = document.getElementById('advance_preview_text');

    function computeRepaymentStartLabel(issuedDate, delayMonths) {
        const d = new Date(issuedDate + 'T12:00:00');
        if (Number.isNaN(d.getTime())) return '—';
        d.setDate(1);
        d.setMonth(d.getMonth() + parseInt(delayMonths || '0', 10));
        const m = String(d.getMonth() + 1).padStart(2, '0');
        return m + '/' + d.getFullYear();
    }

    function updatePreview() {
        const opt = emp.options[emp.selectedIndex];
        const salary = parseFloat(opt?.dataset?.salary || '0');
        const amount = parseFloat(total.value || '0');
        const n = parseInt(count.value || '0', 10);
        const delayMonths = parseInt(delay.value || '0', 10);
        const startLabel = computeRepaymentStartLabel(issuedAt.value, delayMonths);
        if (!amount || !n || n < 2) {
            preview.style.display = 'none';
            return;
        }
        const per = Math.round((amount / n) * 100) / 100;
        preview.style.display = 'block';
        previewText.innerHTML = @json(__('employee_advance.preview_html'))
            .replace(':per', per.toFixed(2))
            .replace(':n', String(n))
            .replace(':total', amount.toFixed(2))
            .replace(':salary', salary.toFixed(2))
            .replace(':start', startLabel)
            .replace(':delay', String(delayMonths));
    }

    emp.addEventListener('change', updatePreview);
    total.addEventListener('input', updatePreview);
    count.addEventListener('change', updatePreview);
    issuedAt.addEventListener('change', updatePreview);
    delay.addEventListener('change', updatePreview);
    updatePreview();
})();
</script>
@endsection
