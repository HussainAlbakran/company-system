@extends('layouts.app')

@section('page_title', __('leaves.create_title'))
@section('page_subtitle', __('leaves.create_subtitle'))

@section('content')

<div class="page-card">

    <div class="page-header">
        <h2>{{ __('leaves.create_title') }}</h2>
        <p>{{ __('leaves.create_subtitle') }}</p>
    </div>

    @if($errors->any())
        <div class="alert-danger" style="margin-bottom:16px;">
            <ul style="margin:0; padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(!($canSubmit ?? true))
        <div class="alert-danger" style="margin-bottom:16px;">
            {{ __('leaves.error_employee_required') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert-success" style="margin-bottom:16px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-danger" style="margin-bottom:16px;">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('leaves.store') }}" method="POST">
        @csrf

        <div class="form-grid">

            <div class="form-group">
                <label>{{ __('leaves.field_employee') }}</label>
                @if($canChooseEmployee)
                    <select name="employee_id" required @disabled(!($canSubmit ?? true))>
                        <option value="">{{ __('leaves.select_employee') }}</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="text" value="{{ optional($employees->first())->name }}" readonly>
                    @if(($canSubmit ?? true) && $employees->isNotEmpty())
                        <input type="hidden" name="employee_id" value="{{ $employees->first()->id }}">
                    @endif
                @endif
            </div>

            <div class="form-group">
                <label>{{ __('leaves.field_start') }}</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" required @disabled(!($canSubmit ?? true))>
            </div>

            <div class="form-group">
                <label>{{ __('leaves.field_end') }}</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" required @disabled(!($canSubmit ?? true))>
            </div>

            <div class="form-group form-group-full">
                <label>{{ __('leaves.field_reason') }}</label>
                <textarea name="reason" rows="4" @disabled(!($canSubmit ?? true))>{{ old('reason') }}</textarea>
            </div>

        </div>

        <div class="actions-row" style="margin-top: 20px;">
            @if($canSubmit ?? true)
                <button type="submit" class="btn btn-primary">
                    {{ __('leaves.submit_request') }}
                </button>
            @endif

            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                {{ __('common.back') }}
            </a>
        </div>
    </form>

</div>

@endsection
