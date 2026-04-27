@extends('layouts.app')

@section('page_title', __('contracts.show_title'))
@section('page_subtitle', __('contracts.show_subtitle'))

@section('content')
@php
    $u = auth()->user();
    $finFull = $u->canViewProjectFinancials();
    $finValueOnly = $u->canViewProjectValueOnly();
@endphp

<div class="page-card">

    <div class="page-header">
        <h2>{{ __('contracts.show_title') }}</h2>
        <p>{{ __('contracts.show_subtitle') }}</p>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-danger">
            <ul style="margin:0; padding-right:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="actions-row" style="margin-bottom: 20px;">
        <a href="{{ route('sales-contracts.index') }}" class="btn btn-secondary">{{ __('common.back') }}</a>
        <a href="{{ route('sales-contracts.edit', $contract->id) }}" class="btn btn-warning">{{ __('contracts.edit') }}</a>
        @if($finFull)
        <button type="button" class="btn btn-primary" onclick="togglePaymentForm()">
            ➕ {{ __('contracts.add_payment') }}
        </button>
        @endif
    </div>

    <div class="details-grid">

        <div class="detail-box">
            <strong>{{ __('contracts.field_contract_no') }}</strong>
            <div>{{ $contract->contract_no }}</div>
        </div>

        <div class="detail-box">
            <strong>{{ __('contracts.field_contract_date') }}</strong>
            <div>{{ $contract->contract_date }}</div>
        </div>

        <div class="detail-box">
            <strong>{{ __('contracts.field_client_name') }}</strong>
            <div>{{ $contract->client_name }}</div>
        </div>

        <div class="detail-box">
            <strong>{{ __('contracts.field_main_contractor') }}</strong>
            <div>{{ $contract->main_contractor ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>{{ __('contracts.field_project_name') }}</strong>
            <div>{{ $contract->project_name }}</div>
        </div>

        <div class="detail-box">
            <strong>{{ __('contracts.field_project_location') }}</strong>
            <div>{{ $contract->project_location ?? '-' }}</div>
        </div>

        @if($finFull || $finValueOnly)
        <div class="detail-box">
            <strong>{{ __('contracts.field_project_value') }}</strong>
            <div>{{ number_format($contract->project_value ?? 0, 2) }}</div>
        </div>
        @endif

        <div class="detail-box">
            <strong>{{ __('contracts.field_project_duration') }}</strong>
            <div>{{ $contract->project_duration ? $contract->project_duration . ' ' . __('contracts.day_suffix') : '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>{{ __('contracts.field_expected_start') }}</strong>
            <div>{{ $contract->expected_start_date ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>{{ __('contracts.field_actual_start') }}</strong>
            <div>{{ $contract->actual_start_date ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>{{ __('contracts.field_expected_end') }}</strong>
            <div>{{ $contract->expected_end_date ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>{{ __('contracts.th_status') }}</strong>
            <div>
                <span class="badge badge-green">{{ $contract->status }}</span>
            </div>
        </div>

        <div class="detail-box">
            <strong>{{ __('contracts.th_stage') }}</strong>
            <div>
                @if($contract->project)
                    <span class="badge badge-blue">{{ $contract->project->current_stage }}</span>
                @else
                    -
                @endif
            </div>
        </div>

        @if($finFull)
        <div class="detail-box">
            <strong>{{ __('contracts.payment_method') }}</strong>
            <div>
                @if($contract->payment_type === 'full')
                    {{ __('contracts.payment_full') }}
                @elseif($contract->payment_type === 'installments')
                    {{ __('contracts.payment_installments') }}
                @else
                    -
                @endif
            </div>
        </div>

        <div class="detail-box">
            <strong>{{ __('contracts.total_paid') }}</strong>
            <div>{{ number_format($contract->total_paid ?? 0, 2) }}</div>
        </div>

        <div class="detail-box">
            <strong>{{ __('contracts.remaining') }}</strong>
            <div>{{ number_format($contract->remaining_amount ?? 0, 2) }}</div>
        </div>
        @endif

        @if($finFull)
        <div class="detail-box">
            <strong>{{ __('contracts.first_payment_recorded_q') }}</strong>
            <div>
                @if($contract->hasFirstPayment())
                    <span class="badge badge-green">{{ __('contracts.yes') }}</span>
                @else
                    <span class="badge badge-gray">{{ __('contracts.no') }}</span>
                @endif
            </div>
        </div>
        @endif

        <div class="detail-box">
            <strong>{{ __('contracts.project_number') }}</strong>
            <div>{{ $contract->project->project_code ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>{{ __('contracts.contract_creator') }}</strong>
            <div>{{ $contract->creator->name ?? '-' }}</div>
        </div>

        <div class="detail-box detail-box-full">
            <strong>{{ __('contracts.field_project_description') }}</strong>
            <div>{{ $contract->description ?? '-' }}</div>
        </div>

        <div class="detail-box detail-box-full">
            <strong>{{ __('contracts.field_notes') }}</strong>
            <div>{{ $contract->notes ?? '-' }}</div>
        </div>

        <div class="detail-box detail-box-full">
            <strong>{{ __('contracts.field_contract_file') }}</strong>
            <div>
                @if($contract->contract_file)
                    <a href="{{ asset('storage/' . $contract->contract_file) }}" target="_blank" class="btn btn-primary">
                        {{ __('contracts.open_contract_file') }}
                    </a>
                @else
                    {{ __('contracts.no_file_uploaded') }}
                @endif
            </div>
        </div>

    </div>
</div>

@if($finFull && $contract->payment_type === 'installments')
<div class="page-card" style="margin-top:24px;">
    <div class="page-header">
        <h2>{{ __('contracts.first_payment_section') }}</h2>
    </div>

    <div class="details-grid">
        <div class="detail-box">
            <strong>{{ __('contracts.first_payment_name') }}</strong>
            <div>{{ $contract->first_payment_title ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>{{ __('contracts.first_payment_pct_label') }}</strong>
            <div>{{ $contract->first_payment_percentage ?? '-' }}%</div>
        </div>

        <div class="detail-box">
            <strong>{{ __('contracts.first_payment_amount_label') }}</strong>
            <div>{{ number_format($contract->first_payment_amount ?? 0, 2) }}</div>
        </div>

        <div class="detail-box">
            <strong>{{ __('contracts.first_payment_due_label') }}</strong>
            <div>{{ $contract->first_payment_due_date ?? '-' }}</div>
        </div>
    </div>
</div>
@endif

@if($finFull)
<div class="page-card" style="margin-top:24px;">
    <div class="page-header">
        <h2>{{ __('contracts.add_payment_section') }}</h2>
        <p>{{ __('contracts.add_payment_hint') }}</p>
    </div>

    <form id="paymentForm" action="{{ route('contract-payments.store', $contract->id) }}" method="POST" style="display:none;">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label>{{ __('contracts.field_amount') }}</label>
                <input type="number" step="0.01" name="amount" class="form-control" required>
            </div>

            <div class="form-group">
                <label>{{ __('contracts.field_payment_date') }}</label>
                <input type="date" name="payment_date" class="form-control" required>
            </div>

            <div class="form-group form-group-full">
                <label>{{ __('contracts.field_notes') }}</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">{{ __('contracts.save_payment') }}</button>
        </div>
    </form>
</div>

<div class="page-card" style="margin-top:24px;">
    <div class="page-header">
        <h2>{{ __('contracts.payments_log') }}</h2>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('contracts.th_payment_type') }}</th>
                    <th>{{ __('contracts.th_amount') }}</th>
                    <th>{{ __('contracts.th_payment_date_col') }}</th>
                    <th>{{ __('contracts.th_notes') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contract->payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>
                            @if($payment->payment_type === 'full')
                                {{ __('contracts.payment_type_full_row') }}
                            @else
                                {{ __('contracts.payment_type_installment_row') }}
                            @endif
                        </td>
                        <td>{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->payment_date }}</td>
                        <td>{{ $payment->notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-row">{{ __('contracts.payments_empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

<script>
function togglePaymentForm() {
    const form = document.getElementById('paymentForm');
    if (!form) return;
    form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
}
</script>

@endsection
