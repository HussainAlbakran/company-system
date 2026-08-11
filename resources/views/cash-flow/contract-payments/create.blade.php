@extends('layouts.app')

@section('page_title', __('cash_flow.contract_payments_page_title'))
@section('page_subtitle', __('cash_flow.contract_payments_page_subtitle'))

@section('content')
<div class="cash-flow-page">
    <x-ui.card :title="__('cash_flow.contract_payments_card_title')" :subtitle="__('cash_flow.contract_payments_card_subtitle')">

        @if(session('success'))
            <div class="alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert-danger" style="margin-bottom:16px;">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert-danger" style="margin-bottom:16px;">
                <ul style="margin:0; padding-right:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="cf-breakdown-grid" style="grid-template-columns: 1fr; gap:12px;">
            <div class="cf-breakdown-card">
                <form method="GET" action="{{ route('cash-flow.contract-payments.create') }}">
                    <div class="form-grid">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label>{{ __('cash_flow.field_project') }}</label>
                            <select name="project_id" class="form-control" onchange="this.form.submit()">
                                <option value="">{{ __('cash_flow.select_project_placeholder') }}</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}" @selected((int) $selectedProjectId === (int) $p->id)>
                                        {{ $p->name }} ({{ $p->project_code ?? '-' }}) — {{ $p->client_name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="cf-breakdown-card">
                <h4 style="margin:0 0 12px;">{{ __('cash_flow.add_contract_payment_title') }}</h4>

                @if(! $contract)
                    <p style="margin:0; color:#6b7280;">{{ __('cash_flow.contract_payment_pick_project_hint') }}</p>
                @else
                    <div style="margin-bottom:12px;">
                        <strong>{{ __('contracts.field_contract_no') }}:</strong> {{ $contract->contract_no }}
                    </div>

                    <form method="POST" action="{{ route('cash-flow.contract-payments.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $contract->project?->id ?? $selectedProjectId }}">

                        <div class="form-grid">
                            <div class="form-group">
                                <label>{{ __('cash_flow.field_amount') }}</label>
                                <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required value="{{ old('amount') }}">
                            </div>

                            <div class="form-group">
                                <label>{{ __('cash_flow.field_date') }}</label>
                                <input type="date" name="payment_date" class="form-control" required value="{{ old('payment_date', now()->toDateString()) }}">
                            </div>

                            <div class="form-group form-group-full">
                                <label>{{ __('cash_flow.field_notes') }}</label>
                                <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="form-actions" style="margin-top:10px;">
                            <button type="submit" class="btn btn-primary">{{ __('cash_flow.save_contract_payment') }}</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        @if($contract)
            <div class="page-card" style="margin-top:18px;">
                <div class="page-header">
                    <h2 style="font-size:16px; margin:0;">{{ __('cash_flow.contract_payments_list_title') }}</h2>
                    <p style="margin:0; font-size:12px; color:#6b7280;">{{ __('cash_flow.contract_payments_list_subtitle') }}</p>
                </div>

                <div class="table-wrap" style="margin-top:12px;">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('cash_flow.field_amount') }}</th>
                                <th>{{ __('cash_flow.field_date') }}</th>
                                <th>{{ __('cash_flow.field_notes') }}</th>
                                <th>{{ __('cash_flow.th_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contract->payments->sortByDesc('payment_date') as $pay)
                                <tr>
                                    <td style="color:#000; font-weight:700;">{{ $loop->iteration }}</td>
                                    <td style="color:#000; font-weight:600;">{{ number_format((float) $pay->amount, 2) }}</td>
                                    <td style="color:#000;">{{ optional($pay->payment_date)->format('Y-m-d') ?? '-' }}</td>
                                    <td style="color:#000;">{{ $pay->notes ?? '-' }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('cash-flow.contract-payments.destroy', $pay) }}"
                                              onsubmit="return confirm(@json(__('cash_flow.confirm_delete_payment')))">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" type="submit">{{ __('cash_flow.delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="empty-row">{{ __('cash_flow.contract_payments_empty') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </x-ui.card>
</div>
@endsection

