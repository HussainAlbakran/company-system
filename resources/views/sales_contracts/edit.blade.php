@extends('layouts.app')

@section('page_title', __('contracts.edit_title'))
@section('page_subtitle', __('contracts.edit_subtitle'))

@section('content')
@php
    $u = auth()->user();
    $showContractValueField = $u->canViewProjectFinancials() || $u->canViewProjectValueOnly();
    $finFull = $u->canViewProjectFinancials();
@endphp

<div class="page-card" id="contracts-edit-page">
    <div class="page-header">
        <div>
            <h2 class="page-title">{{ __('contracts.edit_title') }}</h2>
            <p class="page-subtitle">{{ __('contracts.edit_subtitle') }}</p>
        </div>
    </div>

    <form action="{{ route('sales-contracts.update', $contract->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-grid">

            <div class="form-group">
                <label>{{ __('contracts.field_contract_no') }}</label>
                <input type="text" name="contract_no" value="{{ old('contract_no', $contract->contract_no) }}" required>
            </div>

            <div class="form-group">
                <label>{{ __('contracts.field_contract_date') }}</label>
                <input type="date" name="contract_date" value="{{ old('contract_date', $contract->contract_date) }}" required>
            </div>

            <div class="form-group">
                <label>{{ __('contracts.field_client_name') }}</label>
                <input type="text" name="client_name" value="{{ old('client_name', $contract->client_name) }}" required>
            </div>

            <div class="form-group">
                <label>{{ __('contracts.field_main_contractor') }}</label>
                <input type="text" name="main_contractor" value="{{ old('main_contractor', $contract->main_contractor) }}">
            </div>

            <div class="form-group">
                <label>{{ __('contracts.field_project_name') }}</label>
                <input type="text" name="project_name" value="{{ old('project_name', $contract->project_name) }}" required>
            </div>

            <div class="form-group">
                <label>{{ __('contracts.field_project_location') }}</label>
                <input type="text" name="project_location" value="{{ old('project_location', $contract->project_location) }}">
            </div>

            @if($showContractValueField)
            <div class="form-group">
                <label>{{ __('contracts.field_project_value') }}</label>
                <input type="number" step="0.01" name="project_value" id="project_value" value="{{ old('project_value', $contract->project_value) }}">
            </div>
            @else
            <input type="hidden" name="project_value" value="{{ old('project_value', $contract->project_value) }}">
            @endif

            <div class="form-group">
                <label>{{ __('contracts.field_project_duration') }}</label>
                <input type="number" name="project_duration" value="{{ old('project_duration', $contract->project_duration) }}">
            </div>

            <div class="form-group">
                <label>{{ __('contracts.field_expected_start') }}</label>
                <input type="date" name="expected_start_date" value="{{ old('expected_start_date', $contract->expected_start_date) }}">
            </div>

            <div class="form-group">
                <label>{{ __('contracts.field_actual_project_date') }}</label>
                <input type="date" name="actual_start_date" value="{{ old('actual_start_date', $contract->actual_start_date) }}">
            </div>

            <div class="form-group form-group-full">
                <label>{{ __('contracts.field_project_description') }}</label>
                <textarea name="description">{{ old('description', $contract->description) }}</textarea>
            </div>

            <div class="form-group form-group-full">
                <label>{{ __('contracts.field_notes') }}</label>
                <textarea name="notes">{{ old('notes', $contract->notes) }}</textarea>
            </div>

            <div class="form-group form-group-full">
                <label>{{ __('contracts.field_new_contract_file') }}</label>
                <input type="file" name="contract_file" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png">
                <small style="display:block; margin-top:6px; color:#4b5563;">
                    {{ __('contracts.field_contract_file_hint', ['max' => $maxUploadMb ?? '2']) }}
                </small>
            </div>

            @if($finFull)
                @include('sales_contracts._payment-fields', [
                    'finFull' => true,
                    'selectedPaymentType' => $contract->payment_type,
                    'fullPaymentAmount' => $contract->full_payment_amount,
                    'firstPaymentTitle' => $contract->first_payment_title,
                    'firstPaymentPercentage' => $contract->first_payment_percentage,
                    'firstPaymentAmount' => $contract->first_payment_amount,
                    'firstPaymentDueDate' => $contract->first_payment_due_date,
                ])
            @elseif($u->canViewProjectValueOnly())
                @include('sales_contracts._payment-fields', [
                    'finFull' => false,
                    'selectedPaymentType' => $contract->payment_type,
                ])
            @else
                <input type="hidden" name="payment_type" value="{{ $contract->payment_type }}">
            @endif

        </div>

        <div class="actions-row" style="margin-top: 20px;">
            <button type="submit" class="btn btn-primary">{{ __('contracts.save_changes') }}</button>
            <a href="{{ route('sales-contracts.show', $contract->id) }}" class="btn btn-secondary">{{ __('common.back') }}</a>
        </div>
    </form>
</div>

@endsection
