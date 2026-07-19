@extends('layouts.app')

@section('page_title', __('financial_custody.issue_title'))
@section('page_subtitle', __('financial_custody.issue_subtitle'))

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between;">
        <h1 class="page-title">{{ __('financial_custody.issue_title') }}</h1>
        <a href="{{ route('financial-custodies.index') }}" class="btn btn-secondary btn-sm">{{ __('financial_custody.back') }}</a>
    </div>

    <form method="post" action="{{ route('financial-custodies.store') }}" class="form-grid" style="margin-top:16px;">
        @csrf
        <p class="panel-subtitle" style="grid-column:1/-1; margin:0 0 8px;">{{ __('financial_custody.carryover_issue_hint') }}</p>
        <div class="form-group">
            <label>{{ __('financial_custody.employee') }}</label>
            <select name="employee_id" required>
                <option value="">—</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(old('employee_id')==$emp->id)>{{ $emp->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>{{ __('financial_custody.amount') }}</label>
            <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required>
        </div>
        <div class="form-group">
            <label>{{ __('financial_custody.issued_at') }}</label>
            <input type="date" name="issued_at" value="{{ old('issued_at', now()->format('Y-m-d')) }}">
        </div>
        <div class="form-group" style="grid-column:1/-1;">
            <label>{{ __('financial_custody.notes') }}</label>
            <textarea name="notes" rows="3">{{ old('notes') }}</textarea>
        </div>
        <div style="grid-column:1/-1;">
            <button type="submit" class="btn btn-primary">{{ __('financial_custody.submit_issue') }}</button>
        </div>
    </form>
</div>
@endsection
