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

            @include('sales_contracts._payment-fields', ['finFull' => $finFull])

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

@endsection
