<?php

namespace App\Services;

use App\Models\AssetMaintenanceLog;
use App\Models\CashFlowEntry;
use App\Models\ContractPayment;
use App\Models\EmployeeAdvance;
use App\Models\EmployeeAdvancePayment;
use App\Models\FinancialCustody;
use App\Models\FinancialCustodyTransaction;
use App\Models\Purchase;
use App\Models\PayrollRegister;
use App\Models\SalesContract;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class CashFlowLedgerService
{
    private function hasLedgerSourceColumns(): bool
    {
        // If migrations haven't added `source_type/source_id` yet, skip auto-sync to avoid SQL errors.
        return Schema::hasColumn('cash_flow_entries', 'source_type')
            && Schema::hasColumn('cash_flow_entries', 'source_id');
    }

    public function syncAllMissing(): void
    {
        if (! $this->hasLedgerSourceColumns()) {
            return;
        }

        AssetMaintenanceLog::query()->orderBy('id')->chunk(100, function ($logs) {
            foreach ($logs as $log) {
                $this->syncMaintenanceLog($log);
            }
        });

        PayrollRegister::query()
            ->where('status', 'approved')
            ->orderBy('id')
            ->each(fn (PayrollRegister $register) => $this->syncPayrollRegister($register));

        ContractPayment::query()->orderBy('id')->chunk(100, function ($payments) {
            foreach ($payments as $payment) {
                $this->syncContractPayment($payment);
            }
        });

        Purchase::query()->orderBy('id')->chunk(100, function ($purchases) {
            foreach ($purchases as $purchase) {
                $this->syncPurchase($purchase);
            }
        });

        EmployeeAdvancePayment::query()->orderBy('id')->chunk(100, function ($payments) {
            foreach ($payments as $payment) {
                $this->syncAdvancePayment($payment);
            }
        });
    }

    public function syncMaintenanceLog(AssetMaintenanceLog $log): ?CashFlowEntry
    {
        if (! $this->hasLedgerSourceColumns()) {
            return null;
        }

        if ((float) $log->maintenance_cost <= 0) {
            return null;
        }

        return CashFlowEntry::updateOrCreate(
            [
                'source_type' => CashFlowEntry::SOURCE_ASSET_MAINTENANCE,
                'source_id' => $log->id,
            ],
            [
                'type' => CashFlowEntry::TYPE_EXPENSE,
                'title' => __('cash_flow.auto_maintenance_title', ['asset' => $log->asset_name]),
                'category' => CashFlowEntry::CATEGORY_MAINTENANCE,
                'amount' => $log->maintenance_cost,
                'entry_date' => $log->maintenance_date ?? $log->created_at?->toDateString() ?? now()->toDateString(),
                'notes' => $log->notes,
                'recorded_by' => $log->recorded_by,
            ]
        );
    }

    public function syncPayrollRegister(PayrollRegister $register): ?CashFlowEntry
    {
        if (! $this->hasLedgerSourceColumns()) {
            return null;
        }

        if ($register->status !== 'approved') {
            return null;
        }

        $total = app(PayrollRegisterTotalService::class)->calculateMonthTotal(
            (int) $register->month,
            (int) $register->year
        );

        if ($total <= 0) {
            return null;
        }

        $entryDate = $register->approved_at
            ? Carbon::parse($register->approved_at)->toDateString()
            : Carbon::create((int) $register->year, (int) $register->month, 1)->endOfMonth()->toDateString();

        return CashFlowEntry::updateOrCreate(
            [
                'source_type' => CashFlowEntry::SOURCE_PAYROLL_REGISTER,
                'source_id' => $register->id,
            ],
            [
                'type' => CashFlowEntry::TYPE_EXPENSE,
                'title' => __('cash_flow.auto_payroll_title', [
                    'month' => sprintf('%02d', $register->month),
                    'year' => $register->year,
                ]),
                'category' => CashFlowEntry::CATEGORY_PAYROLL,
                'amount' => $total,
                'entry_date' => $entryDate,
                'notes' => __('cash_flow.auto_payroll_note'),
                'recorded_by' => $register->approved_by,
            ]
        );
    }

    public function syncContractPayment(ContractPayment $payment): ?CashFlowEntry
    {
        if (! $this->hasLedgerSourceColumns()) {
            return null;
        }

        if ((float) $payment->amount <= 0) {
            return null;
        }

        $contract = $payment->contract ?? SalesContract::find($payment->sales_contract_id);
        $contractNo = $contract?->contract_no ?? $payment->sales_contract_id;

        return CashFlowEntry::updateOrCreate(
            [
                'source_type' => CashFlowEntry::SOURCE_CONTRACT_PAYMENT,
                'source_id' => $payment->id,
            ],
            [
                'type' => CashFlowEntry::TYPE_INCOME,
                'title' => __('cash_flow.auto_contract_title', ['contract' => $contractNo]),
                'category' => CashFlowEntry::CATEGORY_CONTRACTS,
                'amount' => $payment->amount,
                'entry_date' => $payment->payment_date ?? $payment->created_at?->toDateString() ?? now()->toDateString(),
                'notes' => $payment->notes,
                'recorded_by' => null,
            ]
        );
    }

    public function removeContractPayment(ContractPayment $payment): void
    {
        if (! $this->hasLedgerSourceColumns()) {
            return;
        }

        CashFlowEntry::query()
            ->where('source_type', CashFlowEntry::SOURCE_CONTRACT_PAYMENT)
            ->where('source_id', $payment->id)
            ->delete();
    }

    public function syncPurchase(Purchase $purchase): ?CashFlowEntry
    {
        if (! $this->hasLedgerSourceColumns()) {
            return null;
        }

        if ((float) $purchase->cost <= 0) {
            return null;
        }

        return CashFlowEntry::updateOrCreate(
            [
                'source_type' => CashFlowEntry::SOURCE_PURCHASE,
                'source_id' => $purchase->id,
            ],
            [
                'type' => CashFlowEntry::TYPE_EXPENSE,
                'title' => __('cash_flow.auto_purchase_title', ['title' => $purchase->title]),
                'category' => CashFlowEntry::CATEGORY_PURCHASES,
                'amount' => $purchase->cost,
                'entry_date' => $purchase->purchase_date ?? $purchase->created_at?->toDateString() ?? now()->toDateString(),
                'notes' => $purchase->notes,
                'recorded_by' => $purchase->created_by,
            ]
        );
    }

    public function removePurchase(Purchase $purchase): void
    {
        if (! $this->hasLedgerSourceColumns()) {
            return;
        }

        CashFlowEntry::query()
            ->where('source_type', CashFlowEntry::SOURCE_PURCHASE)
            ->where('source_id', $purchase->id)
            ->delete();
    }

    public function syncFinancialCustody(FinancialCustody $custody): ?CashFlowEntry
    {
        if (! $this->hasLedgerSourceColumns()) {
            return null;
        }

        $custody->loadMissing('employee');
        $amount = $custody->newCashAmount();

        if ($amount <= 0) {
            return null;
        }

        return CashFlowEntry::updateOrCreate(
            [
                'source_type' => CashFlowEntry::SOURCE_FINANCIAL_CUSTODY,
                'source_id' => $custody->id,
            ],
            [
                'type' => CashFlowEntry::TYPE_EXPENSE,
                'title' => __('cash_flow.auto_custody_title', ['employee' => $custody->employee?->name ?? $custody->employee_id]),
                'category' => CashFlowEntry::CATEGORY_FINANCIAL_CUSTODY,
                'amount' => $amount,
                'entry_date' => $custody->issued_at?->toDateString() ?? now()->toDateString(),
                'notes' => $custody->notes,
                'recorded_by' => $custody->issued_by,
            ]
        );
    }

    /**
     * يُحدّث ملاحظات حركة الصرف الأصلية عند كل تسوية (صرف جزئي/كامل) دون تكرار المبلغ.
     */
    public function syncCustodySettlement(FinancialCustodyTransaction $transaction, FinancialCustody $custody): ?CashFlowEntry
    {
        if (! $this->hasLedgerSourceColumns()) {
            return null;
        }

        if (! in_array($transaction->action, [
            FinancialCustodyTransaction::ACTION_PARTIAL_SETTLEMENT,
            FinancialCustodyTransaction::ACTION_FULL_SETTLEMENT,
        ], true)) {
            return null;
        }

        $entry = CashFlowEntry::query()
            ->where('source_type', CashFlowEntry::SOURCE_FINANCIAL_CUSTODY)
            ->where('source_id', $custody->id)
            ->first();

        if (! $entry) {
            return null;
        }

        $line = __('cash_flow.auto_custody_settlement_line', [
            'amount' => number_format((float) $transaction->amount_settled, 2),
            'purchase' => $transaction->purchase_description ?? '-',
            'date' => $transaction->recorded_at?->format('Y-m-d') ?? now()->toDateString(),
        ]);

        $existing = trim((string) ($entry->notes ?? ''));
        $entry->notes = $existing !== '' ? $existing."\n".$line : $line;
        $entry->save();

        return $entry;
    }

    public function syncCustodyReturn(FinancialCustodyTransaction $transaction, FinancialCustody $custody): ?CashFlowEntry
    {
        if (! $this->hasLedgerSourceColumns()) {
            return null;
        }

        if ($transaction->action !== FinancialCustodyTransaction::ACTION_RETURN_REMAINING) {
            return null;
        }

        $amount = (float) $transaction->amount_settled;

        if ($amount <= 0) {
            return null;
        }

        $custody->loadMissing('employee');

        return CashFlowEntry::updateOrCreate(
            [
                'source_type' => CashFlowEntry::SOURCE_FINANCIAL_CUSTODY_RETURN,
                'source_id' => $transaction->id,
            ],
            [
                'type' => CashFlowEntry::TYPE_NEUTRAL,
                'title' => __('cash_flow.auto_custody_return_title', [
                    'employee' => $custody->employee?->name ?? $custody->employee_id,
                ]),
                'category' => CashFlowEntry::CATEGORY_FINANCIAL_CUSTODY,
                'amount' => $amount,
                'entry_date' => $transaction->recorded_at?->toDateString() ?? now()->toDateString(),
                'notes' => $transaction->notes ?? __('cash_flow.auto_custody_return_note'),
                'recorded_by' => $transaction->recorded_by,
            ]
        );
    }

    public function syncAdvancePayment(EmployeeAdvancePayment $payment): ?CashFlowEntry
    {
        if (! $this->hasLedgerSourceColumns()) {
            return null;
        }

        $amount = (float) $payment->amount;

        if ($amount <= 0) {
            return null;
        }

        $payment->loadMissing(['advance.employee', 'payrollRegister']);
        $advance = $payment->advance;
        $employeeName = $advance?->employee?->name ?? $advance?->employee_id ?? '-';
        $registerLabel = $payment->payrollRegister?->periodLabel()
            ?? ((int) $payment->month).'/'.(int) $payment->year;

        return CashFlowEntry::updateOrCreate(
            [
                'source_type' => CashFlowEntry::SOURCE_ADVANCE_PAYMENT,
                'source_id' => $payment->id,
            ],
            [
                'type' => CashFlowEntry::TYPE_NEUTRAL,
                'title' => __('cash_flow.auto_advance_payment_title', [
                    'employee' => $employeeName,
                    'month' => sprintf('%02d', (int) $payment->month),
                    'year' => $payment->year,
                ]),
                'category' => CashFlowEntry::CATEGORY_ADVANCE_REPAYMENT,
                'amount' => $amount,
                'entry_date' => $payment->recorded_at?->toDateString() ?? now()->toDateString(),
                'notes' => __('cash_flow.auto_advance_payment_note', [
                    'register' => $registerLabel,
                    'installment' => ($advance?->installments_paid ?? 0).' / '.($advance?->installment_count ?? 0),
                ]),
                'recorded_by' => $payment->recorded_by,
            ]
        );
    }

    public function syncEmployeeAdvance(EmployeeAdvance $advance): ?CashFlowEntry
    {
        if (! $this->hasLedgerSourceColumns()) {
            return null;
        }

        $advance->loadMissing('employee');
        $amount = (float) $advance->total_amount;

        if ($amount <= 0) {
            return null;
        }

        return CashFlowEntry::updateOrCreate(
            [
                'source_type' => CashFlowEntry::SOURCE_EMPLOYEE_ADVANCE,
                'source_id' => $advance->id,
            ],
            [
                'type' => CashFlowEntry::TYPE_EXPENSE,
                'title' => __('cash_flow.auto_advance_title', ['employee' => $advance->employee?->name ?? $advance->employee_id]),
                'category' => CashFlowEntry::CATEGORY_ADVANCE,
                'amount' => $amount,
                'entry_date' => $advance->issued_at?->toDateString() ?? now()->toDateString(),
                'notes' => $advance->notes,
                'recorded_by' => $advance->issued_by,
            ]
        );
    }
}
