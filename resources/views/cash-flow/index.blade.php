@extends('layouts.app')

@section('page_title', __('cash_flow.page_title'))
@section('page_subtitle', __('cash_flow.page_subtitle'))

@section('content')
<style>
    .cash-flow-page .cf-kpi-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        margin-bottom: 20px;
    }

    .cash-flow-page .cf-kpi {
        background: #fff;
        border: 1px solid #000;
        border-radius: 10px;
        padding: 14px 16px;
        color: #111827;
    }

    .cash-flow-page .cf-kpi-label {
        font-size: 11px;
        font-weight: 700;
        color: #4b5563;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .cash-flow-page .cf-kpi-value {
        margin-top: 8px;
        font-size: 22px;
        font-weight: 800;
        line-height: 1.2;
    }

    .cash-flow-page .cf-kpi-income .cf-kpi-value { color: #15803d; }
    .cash-flow-page .cf-kpi-expense .cf-kpi-value { color: #b91c1c; }
    .cash-flow-page .cf-kpi-balance .cf-kpi-value { color: #111827; }
    .cash-flow-page .cf-kpi-balance.negative .cf-kpi-value { color: #b91c1c; }

    .cash-flow-page .cf-form-card {
        background: #fff;
        border: 1px solid #000;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 20px;
        color: #111827;
    }

    .cash-flow-page .cf-form-card h3 {
        margin: 0 0 12px;
        font-size: 16px;
        color: #111827;
    }

    .cash-flow-page .amount-income { color: #15803d; font-weight: 700; }
    .cash-flow-page .amount-expense { color: #b91c1c; font-weight: 700; }
    .cash-flow-page .amount-neutral { color: #ea580c; font-weight: 700; }
    .cash-flow-page .cf-kpi-neutral .cf-kpi-value { color: #ea580c; }

    .cash-flow-page .cf-breakdown-grid {
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        margin-bottom: 20px;
    }

    .cash-flow-page .cf-breakdown-card {
        background: #fff;
        border: 1px solid #000;
        border-radius: 10px;
        padding: 14px 16px;
        color: #111827;
    }

    .cash-flow-page .cf-breakdown-card h4 {
        margin: 0 0 12px;
        font-size: 14px;
        font-weight: 800;
    }

    .cash-flow-page .cf-breakdown-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding: 8px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .cash-flow-page .cf-breakdown-row:last-child { border-bottom: 0; }

    .cash-flow-page .cf-breakdown-amount-expense {
        color: #b91c1c;
        font-weight: 800;
        font-size: 14px;
    }

    .cash-flow-page .cf-breakdown-amount-income {
        color: #15803d;
        font-weight: 800;
        font-size: 14px;
    }
</style>

<div class="cash-flow-page">
    <x-ui.card :title="__('cash_flow.page_title')" :subtitle="__('cash_flow.page_subtitle')">

        @if(session('success'))
            <div class="alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert-danger" style="margin-bottom:16px;">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert-danger" style="margin-bottom:16px;">
                <ul style="margin:0; padding-right:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div style="display:flex; flex-wrap:wrap; gap:10px; margin-bottom:18px;">
            <a href="{{ route('financial-custodies.create') }}" class="btn btn-primary btn-sm">{{ __('cash_flow.btn_issue_custody') }}</a>
            <a href="{{ route('financial-custodies.index') }}" class="btn btn-secondary btn-sm">{{ __('cash_flow.btn_custody_records') }}</a>
            <a href="{{ route('custody-settlements.index') }}" class="btn btn-secondary btn-sm">{{ __('navigation.custody_settlement') }}</a>
            <a href="{{ route('custody-settlements.records') }}" class="btn btn-secondary btn-sm">{{ __('cash_flow.btn_settlement_records') }}</a>
            <a href="{{ route('employee-advances.create') }}" class="btn btn-warning btn-sm">{{ __('cash_flow.btn_issue_advance') }}</a>
            <a href="{{ route('employee-advances.index') }}" class="btn btn-secondary btn-sm">{{ __('cash_flow.btn_advance_records') }}</a>
            <a href="{{ route('cash-flow.contract-payments.create') }}" class="btn btn-secondary btn-sm">{{ __('cash_flow.btn_record_contract_payment') }}</a>
        </div>

        <div class="cf-kpi-grid">
            <div class="cf-kpi cf-kpi-income">
                <div class="cf-kpi-label">{{ __('cash_flow.total_income') }}</div>
                <div class="cf-kpi-value">{{ number_format($totalIncome, 2) }}</div>
            </div>
            <div class="cf-kpi cf-kpi-expense">
                <div class="cf-kpi-label">{{ __('cash_flow.total_expense') }}</div>
                <div class="cf-kpi-value">{{ number_format($totalExpense, 2) }}</div>
            </div>
            <div class="cf-kpi cf-kpi-neutral">
                <div class="cf-kpi-label">{{ __('cash_flow.total_neutral') }}</div>
                <div class="cf-kpi-value">{{ number_format($totalNeutral ?? 0, 2) }}</div>
            </div>
            <div class="cf-kpi cf-kpi-balance {{ $balance < 0 ? 'negative' : '' }}">
                <div class="cf-kpi-label">{{ __('cash_flow.net_balance') }}</div>
                <div class="cf-kpi-value">{{ number_format($balance, 2) }}</div>
            </div>
        </div>
        <p style="margin:-8px 0 16px; font-size:12px; color:#6b7280;">{{ __('cash_flow.neutral_hint') }}</p>

        <div class="cf-breakdown-grid">
            <div class="cf-breakdown-card">
                <h4>{{ __('cash_flow.expense_breakdown_title') }}</h4>
                @forelse($expenseBreakdown as $row)
                    <div class="cf-breakdown-row">
                        <span>{{ $row['category'] }}</span>
                        <span class="cf-breakdown-amount-expense">-{{ number_format($row['amount'], 2) }}</span>
                    </div>
                    @if($row['url'])
                        <div style="margin: -4px 0 8px;">
                            <a href="{{ $row['url'] }}" class="btn btn-secondary btn-sm">{{ $row['link_label'] ?? __('cash_flow.view_category_list', ['category' => $row['category']]) }}</a>
                        </div>
                    @endif
                @empty
                    <p style="margin:0; color:#6b7280; font-size:12px;">{{ __('cash_flow.breakdown_empty') }}</p>
                @endforelse
            </div>

            <div class="cf-breakdown-card">
                <h4>{{ __('cash_flow.income_breakdown_title') }}</h4>
                @forelse($incomeBreakdown as $row)
                    <div class="cf-breakdown-row">
                        <span>{{ $row['category'] }}</span>
                        <span class="cf-breakdown-amount-income">+{{ number_format($row['amount'], 2) }}</span>
                    </div>
                    @if($row['url'])
                        <div style="margin: -4px 0 8px;">
                            <a href="{{ $row['url'] }}" class="btn btn-secondary btn-sm">{{ $row['link_label'] ?? __('cash_flow.view_category_list', ['category' => $row['category']]) }}</a>
                        </div>
                    @endif
                @empty
                    <p style="margin:0; color:#6b7280; font-size:12px;">{{ __('cash_flow.breakdown_empty') }}</p>
                @endforelse
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; margin-bottom:12px;">
            <button type="button" class="btn btn-secondary" onclick="toggleCashFlowManualForm()">
                {{ __('cash_flow.side_register_btn') }}
            </button>
        </div>

        <div id="cfManualFormWrap" style="display:none;">
            <div class="cf-form-card">
                <h3>{{ __('cash_flow.add_entry') }}</h3>
                <form method="POST" action="{{ route('cash-flow.store') }}">
                    @csrf
                    <input type="hidden" name="type" value="{{ old('type', 'income') }}" id="cf_type">

                    <div class="actions-row" style="margin-bottom:12px;">
                        <button type="button" class="btn btn-success btn-sm cf-type-btn" data-type="income">{{ __('cash_flow.type_income') }}</button>
                        <button type="button" class="btn btn-secondary btn-sm cf-type-btn" data-type="expense">{{ __('cash_flow.type_expense') }}</button>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>{{ __('cash_flow.field_title') }}</label>
                            <input type="text" name="title" value="{{ old('title') }}" required placeholder="{{ __('cash_flow.placeholder_title') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('cash_flow.field_category') }}</label>
                            <input type="text" name="category" value="{{ old('category') }}" placeholder="{{ __('cash_flow.placeholder_category') }}">
                        </div>
                        <div class="form-group">
                            <label>{{ __('cash_flow.field_amount') }}</label>
                            <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required>
                        </div>
                        <div class="form-group">
                            <label>{{ __('cash_flow.field_date') }}</label>
                            <input type="date" name="entry_date" value="{{ old('entry_date', now()->toDateString()) }}" required>
                        </div>
                        <div class="form-group form-group-full">
                            <label>{{ __('cash_flow.field_notes') }}</label>
                            <textarea name="notes" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top:10px; display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                        <button type="submit" class="btn btn-primary" id="cf_submit_btn">{{ __('cash_flow.save_income') }}</button>
                        <button type="button" class="btn btn-secondary" onclick="toggleCashFlowManualForm()">
                            {{ __('common.cancel') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="page-card" style="margin-bottom:16px;">
            <form method="GET" action="{{ route('cash-flow.index') }}">
                <div class="form-grid" style="align-items:end;">
                    <div class="form-group">
                        <label>{{ __('cash_flow.filter_type') }}</label>
                        <select name="type">
                            <option value="">{{ __('cash_flow.filter_all') }}</option>
                            <option value="income" @selected(request('type') === 'income')>{{ __('cash_flow.type_income') }}</option>
                            <option value="expense" @selected(request('type') === 'expense')>{{ __('cash_flow.type_expense') }}</option>
                            <option value="neutral" @selected(request('type') === 'neutral')>{{ __('cash_flow.total_neutral') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ __('cash_flow.date_from') }}</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('cash_flow.date_to') }}</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="form-group">
                        <label>{{ __('cash_flow.search') }}</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('cash_flow.search_placeholder') }}">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">{{ __('cash_flow.apply_filter') }}</button>
                        <a href="{{ route('cash-flow.index') }}" class="btn btn-secondary">{{ __('cash_flow.reset') }}</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="page-header" style="margin-top:6px;">
            <h2 style="margin:0; font-size:18px;">{{ __('cash_flow.ledger_title') }}</h2>
        </div>

        <x-ui.table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('cash_flow.th_type') }}</th>
                    <th>{{ __('cash_flow.field_title') }}</th>
                    <th>{{ __('cash_flow.field_category') }}</th>
                    <th>{{ __('cash_flow.field_amount') }}</th>
                    <th>{{ __('cash_flow.field_date') }}</th>
                    <th>{{ __('cash_flow.field_notes') }}</th>
                    <th>{{ __('cash_flow.recorded_by') }}</th>
                    <th>{{ __('cash_flow.th_actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                    <tr>
                        <td>{{ $entry->id }}</td>
                        <td>
                            @if($entry->isNeutral())
                                <span class="badge" style="background:#ea580c; color:#fff;">{{ $entry->typeLabel() }}</span>
                            @elseif($entry->isIncome())
                                <span class="badge badge-green">{{ __('cash_flow.type_income') }}</span>
                            @else
                                <span class="badge badge-red">{{ __('cash_flow.type_expense') }}</span>
                            @endif
                        </td>
                        <td>{{ $entry->title }}</td>
                        <td>{{ $entry->category ?? '-' }}</td>
                        <td class="{{ $entry->isNeutral() ? 'amount-neutral' : ($entry->isIncome() ? 'amount-income' : 'amount-expense') }}">
                            {{ $entry->signedAmountPrefix() }}{{ number_format((float) $entry->amount, 2) }}
                        </td>
                        <td>{{ $entry->entry_date?->format('Y-m-d') }}</td>
                        <td>{{ $entry->notes ?? '-' }}</td>
                        <td>
                            {{ $entry->recorder->name ?? '-' }}
                            @if($entry->isAuto())
                                <span class="badge badge-blue" style="margin-inline-start:6px;">{{ __('cash_flow.badge_auto') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($entry->isManual())
                                <form action="{{ route('cash-flow.destroy', $entry) }}" method="POST" onsubmit="return confirm(@json(__('cash_flow.confirm_delete')))">
                                    @csrf
                                    @method('DELETE')
                                    @foreach(request()->only(['type', 'date_from', 'date_to', 'search']) as $key => $val)
                                        @if($val !== null && $val !== '')
                                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                                        @endif
                                    @endforeach
                                    <button type="submit" class="btn btn-danger btn-sm">{{ __('cash_flow.delete') }}</button>
                                </form>
                            @else
                                <span style="color:#6b7280; font-size:11px;">{{ __('cash_flow.auto_locked') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty-row">{{ __('cash_flow.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>

        @if(method_exists($entries, 'links'))
            <div style="margin-top:16px;">{{ $entries->links() }}</div>
        @endif

    </x-ui.card>
</div>

<script>
(function () {
    const typeInput = document.getElementById('cf_type');
    const submitBtn = document.getElementById('cf_submit_btn');
    const labels = {
        income: @json(__('cash_flow.save_income')),
        expense: @json(__('cash_flow.save_expense')),
    };

    function setType(type) {
        if (!typeInput) return;
        typeInput.value = type;
        document.querySelectorAll('.cf-type-btn').forEach(function (btn) {
            const active = btn.dataset.type === type;
            btn.classList.toggle('btn-success', active && type === 'income');
            btn.classList.toggle('btn-danger', active && type === 'expense');
            btn.classList.toggle('btn-secondary', !active);
        });
        if (submitBtn) submitBtn.textContent = labels[type] || labels.income;
    }

    document.querySelectorAll('.cf-type-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            setType(btn.dataset.type);
        });
    });

    setType(typeInput?.value || 'income');
})();

function toggleCashFlowManualForm() {
    const wrap = document.getElementById('cfManualFormWrap');
    if (!wrap) return;
    const isHidden = wrap.style.display === 'none' || wrap.style.display === '';
    wrap.style.display = isHidden ? 'block' : 'none';
}
</script>
@endsection
