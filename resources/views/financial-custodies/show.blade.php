@extends('layouts.app')

@section('page_title', __('financial_custody.show_title'))
@section('page_subtitle', $custody->employee->name ?? '')

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px;">
        <div>
            <h1 class="page-title">{{ __('financial_custody.show_title') }}</h1>
            <p style="margin:6px 0 0; color:#6b7280;">{{ $custody->employee->name ?? '-' }}</p>
        </div>
        <a href="{{ route('financial-custodies.index') }}" class="btn btn-secondary btn-sm">{{ __('financial_custody.back') }}</a>
    </div>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-danger">{{ session('error') }}</div>@endif

    <div class="summary-grid" style="display:grid; gap:10px; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); margin:16px 0;">
        <div class="detail-box">
            <strong>{{ __('financial_custody.employee') }}</strong>
            <div>{{ $custody->employee->name ?? '-' }}</div>
        </div>
        <div class="detail-box">
            <strong>{{ __('financial_custody.amount_issued') }}</strong>
            <div>{{ number_format((float) $custody->amount_issued, 2) }}</div>
        </div>
        @if((float) $custody->carried_over_amount > 0)
        <div class="detail-box">
            <strong>{{ __('financial_custody.carried_over_amount') }}</strong>
            <div>{{ number_format((float) $custody->carried_over_amount, 2) }}</div>
        </div>
        <div class="detail-box">
            <strong>{{ __('financial_custody.new_cash_amount') }}</strong>
            <div>{{ number_format($custody->newCashAmount(), 2) }}</div>
        </div>
        @endif
        <div class="detail-box">
            <strong>{{ __('financial_custody.total_spent') }}</strong>
            <div>{{ number_format($custody->totalSpent(), 2) }}</div>
        </div>
        <div class="detail-box">
            <strong>{{ __('financial_custody.total_returned') }}</strong>
            <div>{{ number_format($custody->totalReturned(), 2) }}</div>
        </div>
        <div class="detail-box">
            <strong>{{ __('financial_custody.amount_remaining') }}</strong>
            <div>{{ number_format((float) $custody->amount_remaining, 2) }}</div>
        </div>
        <div class="detail-box">
            <strong>{{ __('financial_custody.issued_at') }}</strong>
            <div>{{ $custody->issued_at?->format('Y-m-d') ?? '-' }}</div>
        </div>
        <div class="detail-box">
            <strong>{{ __('financial_custody.status') }}</strong>
            <div>
                @if($custody->isOpen())
                    <span class="badge badge-orange">{{ __('financial_custody.status_open') }}</span>
                @else
                    <span class="badge badge-green">{{ __('financial_custody.status_closed') }}</span>
                @endif
            </div>
        </div>
    </div>

    @if($custody->notes)
        <div class="detail-box" style="margin-bottom:16px; padding:12px; background:#f9fafb; border-radius:8px;">
            <strong>{{ __('financial_custody.issue_notes') }}</strong>
            <div style="margin-top:6px;">{{ $custody->notes }}</div>
        </div>
    @endif

    @if($custody->isOpen())
        <div style="display:flex; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
            <div class="page-card" style="flex:1; min-width:280px;">
                <h3 style="margin:0 0 12px;">{{ __('financial_custody.btn_partial_settle') }}</h3>
                <form method="post" action="{{ route('financial-custodies.settle-partial', $custody) }}" class="form-grid">
                    @csrf
                    <div class="form-group">
                        <label>{{ __('financial_custody.amount_spent') }}</label>
                        <input type="number" step="0.01" min="0.01" max="{{ (float) $custody->amount_remaining }}" name="amount_spent" value="{{ old('amount_spent') }}" required>
                        <small style="color:#6b7280;">{{ __('financial_custody.max_spent_hint', ['max' => number_format((float) $custody->amount_remaining, 2)]) }}</small>
                    </div>
                    <div class="form-group">
                        <label>{{ __('financial_custody.purchase_description') }} <span style="color:#dc2626;">*</span></label>
                        <textarea name="purchase_description" rows="2" maxlength="2000" required placeholder="{{ __('financial_custody.purchase_placeholder') }}">{{ old('purchase_description') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>{{ __('financial_custody.settle_notes') }}</label>
                        <input type="text" name="notes" maxlength="2000" value="{{ old('notes') }}">
                    </div>
                    <div><button type="submit" class="btn btn-warning btn-sm">{{ __('financial_custody.btn_partial_settle') }}</button></div>
                </form>
            </div>

            <div class="page-card" style="flex:1; min-width:280px;">
                <h3 style="margin:0 0 12px;">{{ __('financial_custody.btn_full_settle') }}</h3>
                <form method="post" action="{{ route('financial-custodies.settle-full', $custody) }}" onsubmit="return confirm(@json(__('financial_custody.confirm_full')))">
                    @csrf
                    <div class="form-group">
                        <label>{{ __('financial_custody.purchase_description') }} <span style="color:#dc2626;">*</span></label>
                        <textarea name="purchase_description" rows="2" maxlength="2000" required placeholder="{{ __('financial_custody.purchase_placeholder') }}"></textarea>
                    </div>
                    <div class="form-group">
                        <label>{{ __('financial_custody.settle_notes') }}</label>
                        <input type="text" name="notes" maxlength="2000">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">{{ __('financial_custody.btn_full_settle') }}</button>
                </form>

                @if((float) $custody->amount_remaining > 0)
                <hr style="margin:16px 0;">
                <h3 style="margin:0 0 12px;">{{ __('financial_custody.btn_return_remaining') }}</h3>
                <form method="post" action="{{ route('financial-custodies.return-remaining', $custody) }}" onsubmit="return confirm(@json(__('financial_custody.confirm_return', ['amount' => number_format((float) $custody->amount_remaining, 2)])))">
                    @csrf
                    <div class="form-group">
                        <label>{{ __('financial_custody.notes') }}</label>
                        <input type="text" name="notes" maxlength="2000" placeholder="{{ __('financial_custody.return_notes_placeholder') }}">
                    </div>
                    <button type="submit" class="btn btn-sm" style="background:#ea580c; color:#fff; border-color:#ea580c;">{{ __('financial_custody.btn_return_remaining') }}</button>
                </form>
                @endif
            </div>
        </div>
    @endif

    <h3>{{ __('financial_custody.transactions_title') }}</h3>
    <p class="panel-subtitle" style="margin-bottom:12px;">{{ __('financial_custody.transactions_hint') }}</p>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>{{ __('financial_custody.th_date') }}</th>
                    <th>{{ __('financial_custody.th_action') }}</th>
                    <th>{{ __('financial_custody.th_settled') }}</th>
                    <th>{{ __('financial_custody.th_remaining') }}</th>
                    <th>{{ __('financial_custody.purchase_description') }}</th>
                    <th>{{ __('financial_custody.notes') }}</th>
                    <th>{{ __('financial_custody.th_recorded_by') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($custody->transactions as $tx)
                    <tr>
                        <td>{{ $tx->recorded_at?->format('Y-m-d H:i') }}</td>
                        <td>
                            @if($tx->action === 'issue') {{ __('financial_custody.action_issue') }}
                            @elseif($tx->action === 'full_settlement') {{ __('financial_custody.action_full') }}
                            @elseif($tx->action === 'return_remaining') {{ __('financial_custody.action_return') }}
                            @elseif($tx->action === 'carryover_out') {{ __('financial_custody.action_carryover') }}
                            @elseif($tx->action === 'carryover_in') {{ __('financial_custody.action_carryover_in') }}
                            @else {{ __('financial_custody.action_partial') }}
                            @endif
                        </td>
                        <td>{{ number_format((float) $tx->amount_settled, 2) }}</td>
                        <td>{{ number_format((float) $tx->amount_remaining_after, 2) }}</td>
                        <td style="text-align:right; max-width:220px;">{{ $tx->purchase_description ?? '-' }}</td>
                        <td>{{ $tx->notes ?? '-' }}</td>
                        <td>{{ $tx->recorder->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7">{{ __('financial_custody.empty_transactions') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
