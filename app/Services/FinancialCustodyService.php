<?php

namespace App\Services;

use App\Helpers\AuditHelper;
use App\Models\FinancialCustody;
use App\Models\FinancialCustodyInvoice;
use App\Models\FinancialCustodySettlement;
use App\Models\FinancialCustodyTransaction;
use Illuminate\Support\Facades\DB;

class FinancialCustodyService
{
    public function __construct(
        protected CashFlowLedgerService $ledger
    ) {}

    public function issue(array $data, int $issuedBy): FinancialCustody
    {
        return DB::transaction(function () use ($data, $issuedBy) {
            $employeeId = (int) $data['employee_id'];
            $newCash = round((float) $data['amount'], 2);

            if ($newCash <= 0) {
                throw new \InvalidArgumentException(__('financial_custody.invalid_amount'));
            }

            $openCustodies = FinancialCustody::query()
                ->where('employee_id', $employeeId)
                ->where('status', FinancialCustody::STATUS_OPEN)
                ->where('amount_remaining', '>', 0)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($openCustodies as $oldCustody) {
                $this->assertCanTransferRemaining($oldCustody);
            }

            $issueNotes = isset($data['notes']) ? trim((string) $data['notes']) : null;

            $custody = FinancialCustody::create([
                'employee_id' => $employeeId,
                'amount_issued' => $newCash,
                'amount_remaining' => $newCash,
                'carried_over_amount' => 0,
                'status' => FinancialCustody::STATUS_OPEN,
                'issued_at' => $data['issued_at'] ?? now()->toDateString(),
                'issued_by' => $issuedBy,
                'notes' => $issueNotes,
            ]);

            FinancialCustodyTransaction::create([
                'financial_custody_id' => $custody->id,
                'action' => FinancialCustodyTransaction::ACTION_ISSUE,
                'amount_settled' => 0,
                'amount_remaining_after' => $newCash,
                'purchase_description' => null,
                'notes' => $issueNotes,
                'recorded_by' => $issuedBy,
                'recorded_at' => now(),
            ]);

            foreach ($openCustodies as $oldCustody) {
                $oldCustody->refresh();
                if (round((float) $oldCustody->amount_remaining, 2) <= 0) {
                    continue;
                }

                $custody = $this->transferRemainingToCustody($oldCustody, $custody, $issuedBy);
            }

            $custody = $custody->fresh();
            $this->ledger->syncFinancialCustody($custody);

            $carryover = round((float) $custody->carried_over_amount, 2);
            $auditMessage = $carryover > 0
                ? 'تسليم عهدة #'.$custody->id.' — نقد جديد: '.$newCash.' — مرحّل: '.$carryover.' — الإجمالي: '.$custody->amount_issued
                : 'تسليم عهدة مالية بمبلغ '.$newCash.' للموظف #'.$custody->employee_id;

            AuditHelper::log(
                'financial_custody_issued',
                'FinancialCustody',
                $custody->id,
                $auditMessage,
                $issuedBy
            );

            return $custody;
        });
    }

    /**
     * بعد اعتماد التصفية: إن وُجدت عهدة أحدث مفتوحة يُضاف المتبقي إليها تلقائياً.
     */
    public function tryCarryOverToNewerCustody(FinancialCustody $fromCustody, int $recordedBy): ?FinancialCustody
    {
        $fromCustody->refresh();

        $remaining = round((float) $fromCustody->amount_remaining, 2);
        if ($remaining <= 0 || ! $fromCustody->isOpen()) {
            return null;
        }

        $toCustody = FinancialCustody::query()
            ->where('employee_id', $fromCustody->employee_id)
            ->where('status', FinancialCustody::STATUS_OPEN)
            ->where('id', '>', $fromCustody->id)
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        if (! $toCustody) {
            return null;
        }

        return $this->transferRemainingToCustody($fromCustody, $toCustody, $recordedBy);
    }

    public function transferRemainingToCustody(
        FinancialCustody $from,
        FinancialCustody $to,
        int $recordedBy
    ): FinancialCustody {
        $from = FinancialCustody::query()->whereKey($from->id)->lockForUpdate()->firstOrFail();
        $to = FinancialCustody::query()->whereKey($to->id)->lockForUpdate()->firstOrFail();

        if ((int) $from->employee_id !== (int) $to->employee_id || (int) $from->id === (int) $to->id) {
            throw new \InvalidArgumentException(__('financial_custody.carryover_invalid_target'));
        }

        $remaining = round((float) $from->amount_remaining, 2);
        if ($remaining <= 0) {
            return $to->fresh();
        }

        if (! $from->isOpen() || ! $to->isOpen()) {
            throw new \InvalidArgumentException(__('financial_custody.already_closed'));
        }

        $from->amount_remaining = 0;
        $from->status = FinancialCustody::STATUS_CLOSED;
        $from->save();

        $to->amount_issued = round((float) $to->amount_issued + $remaining, 2);
        $to->amount_remaining = round((float) $to->amount_remaining + $remaining, 2);
        $to->carried_over_amount = round((float) $to->carried_over_amount + $remaining, 2);

        $carryoverNote = __('financial_custody.carryover_in_note', [
            'amount' => number_format($remaining, 2),
            'ids' => (string) $from->id,
        ]);
        $existingNotes = trim((string) ($to->notes ?? ''));
        $to->notes = $existingNotes !== '' ? $existingNotes."\n".$carryoverNote : $carryoverNote;
        $to->save();

        FinancialCustodyTransaction::create([
            'financial_custody_id' => $from->id,
            'action' => FinancialCustodyTransaction::ACTION_CARRYOVER_OUT,
            'amount_settled' => $remaining,
            'amount_remaining_after' => 0,
            'purchase_description' => null,
            'notes' => __('financial_custody.carryover_out_note', [
                'amount' => number_format($remaining, 2),
                'id' => $to->id,
            ]),
            'recorded_by' => $recordedBy,
            'recorded_at' => now(),
        ]);

        FinancialCustodyTransaction::create([
            'financial_custody_id' => $to->id,
            'action' => FinancialCustodyTransaction::ACTION_CARRYOVER_IN,
            'amount_settled' => $remaining,
            'amount_remaining_after' => round((float) $to->amount_remaining, 2),
            'purchase_description' => null,
            'notes' => $carryoverNote,
            'recorded_by' => $recordedBy,
            'recorded_at' => now(),
        ]);

        AuditHelper::log(
            'financial_custody_carryover_out',
            'FinancialCustody',
            $from->id,
            'ترحيل متبقي '.$remaining.' إلى العهدة #'.$to->id,
            $recordedBy
        );

        AuditHelper::log(
            'financial_custody_carryover_in',
            'FinancialCustody',
            $to->id,
            'استلام مرحّل '.$remaining.' من العهدة #'.$from->id,
            $recordedBy
        );

        return $to->fresh();
    }

    protected function assertCanTransferRemaining(FinancialCustody $custody): void
    {
        if ($custody->draftSettlement()) {
            throw new \InvalidArgumentException(__('financial_custody.carryover_block_draft_settlement'));
        }

        $hasPendingInvoices = FinancialCustodyInvoice::query()
            ->where('financial_custody_id', $custody->id)
            ->whereIn('status', [
                FinancialCustodyInvoice::STATUS_REGISTERED,
                FinancialCustodyInvoice::STATUS_ON_SETTLEMENT,
            ])
            ->exists();

        if ($hasPendingInvoices) {
            throw new \InvalidArgumentException(__('financial_custody.carryover_block_pending_invoices'));
        }

        $hasApprovedSettlement = FinancialCustodySettlement::query()
            ->where('financial_custody_id', $custody->id)
            ->where('status', FinancialCustodySettlement::STATUS_APPROVED)
            ->exists();

        $wasSpent = $custody->totalSpent() > 0;

        if (! $hasApprovedSettlement && ! $wasSpent) {
            throw new \InvalidArgumentException(__('financial_custody.carryover_block_unsettled'));
        }
    }

    /**
     * @return array{carryover: float, custody_count: int}
     */
    public function pendingCarryoverForEmployee(int $employeeId): array
    {
        $custodies = FinancialCustody::query()
            ->where('employee_id', $employeeId)
            ->where('status', FinancialCustody::STATUS_OPEN)
            ->where('amount_remaining', '>', 0)
            ->get();

        $carryover = 0.0;
        $count = 0;

        foreach ($custodies as $custody) {
            try {
                $this->assertCanTransferRemaining($custody);
                $carryover = round($carryover + (float) $custody->amount_remaining, 2);
                $count++;
            } catch (\InvalidArgumentException) {
                continue;
            }
        }

        return ['carryover' => $carryover, 'custody_count' => $count];
    }

    public function settleFull(
        FinancialCustody $custody,
        string $purchaseDescription,
        ?string $notes,
        int $recordedBy
    ): FinancialCustody {
        return $this->applySettlement(
            $custody,
            0,
            FinancialCustodyTransaction::ACTION_FULL_SETTLEMENT,
            $purchaseDescription,
            $notes,
            $recordedBy
        );
    }

    public function settlePartial(
        FinancialCustody $custody,
        float $amountSpent,
        string $purchaseDescription,
        ?string $notes,
        int $recordedBy
    ): FinancialCustody {
        $amountSpent = round($amountSpent, 2);

        return DB::transaction(function () use ($custody, $amountSpent, $purchaseDescription, $notes, $recordedBy) {
            $custody = FinancialCustody::query()->whereKey($custody->id)->lockForUpdate()->firstOrFail();

            if (! $custody->isOpen()) {
                throw new \InvalidArgumentException(__('financial_custody.already_closed'));
            }

            $current = round((float) $custody->amount_remaining, 2);

            if ($amountSpent <= 0 || $amountSpent > $current) {
                throw new \InvalidArgumentException(__('financial_custody.invalid_spent_amount', [
                    'max' => number_format($current, 2),
                ]));
            }

            $remainingAfter = round($current - $amountSpent, 2);

            return $this->applySettlementLocked(
                $custody,
                $remainingAfter,
                $amountSpent,
                FinancialCustodyTransaction::ACTION_PARTIAL_SETTLEMENT,
                $purchaseDescription,
                $notes,
                $recordedBy
            );
        });
    }

    protected function applySettlement(
        FinancialCustody $custody,
        float $remainingAfter,
        string $action,
        string $purchaseDescription,
        ?string $notes,
        int $recordedBy
    ): FinancialCustody {
        return DB::transaction(function () use ($custody, $remainingAfter, $action, $purchaseDescription, $notes, $recordedBy) {
            $custody = FinancialCustody::query()->whereKey($custody->id)->lockForUpdate()->firstOrFail();

            if (! $custody->isOpen()) {
                throw new \InvalidArgumentException(__('financial_custody.already_closed'));
            }

            $before = round((float) $custody->amount_remaining, 2);
            $remainingAfter = round($remainingAfter, 2);

            if ($remainingAfter < 0 || $remainingAfter > $before) {
                throw new \InvalidArgumentException(__('financial_custody.invalid_remaining'));
            }

            $settled = round($before - $remainingAfter, 2);

            if ($settled <= 0) {
                throw new \InvalidArgumentException(__('financial_custody.invalid_spent_amount', [
                    'max' => number_format($before, 2),
                ]));
            }

            return $this->applySettlementLocked(
                $custody,
                $remainingAfter,
                $settled,
                $action,
                $purchaseDescription,
                $notes,
                $recordedBy
            );
        });
    }

    protected function applySettlementLocked(
        FinancialCustody $custody,
        float $remainingAfter,
        float $settled,
        string $action,
        string $purchaseDescription,
        ?string $notes,
        int $recordedBy
    ): FinancialCustody {
        $custody->amount_remaining = $remainingAfter;
        $custody->status = $remainingAfter <= 0
            ? FinancialCustody::STATUS_CLOSED
            : FinancialCustody::STATUS_OPEN;
        $custody->save();

        $transaction = FinancialCustodyTransaction::create([
            'financial_custody_id' => $custody->id,
            'action' => $action,
            'amount_settled' => $settled,
            'amount_remaining_after' => $remainingAfter,
            'purchase_description' => trim($purchaseDescription),
            'notes' => $notes !== null && trim($notes) !== '' ? trim($notes) : null,
            'recorded_by' => $recordedBy,
            'recorded_at' => now(),
        ]);

        $this->ledger->syncCustodySettlement($transaction, $custody);

        AuditHelper::log(
            'financial_custody_settled',
            'FinancialCustody',
            $custody->id,
            'تسوية عهدة #'.$custody->id.' — مصروف: '.$settled.' — المتبقي: '.$remainingAfter,
            $recordedBy
        );

        return $custody->fresh(['employee', 'transactions.recorder']);
    }

    public function returnRemaining(FinancialCustody $custody, ?string $notes, int $recordedBy): FinancialCustody
    {
        return DB::transaction(function () use ($custody, $notes, $recordedBy) {
            $custody = FinancialCustody::query()->whereKey($custody->id)->lockForUpdate()->firstOrFail();

            if (! $custody->isOpen()) {
                throw new \InvalidArgumentException(__('financial_custody.already_closed'));
            }

            $returned = round((float) $custody->amount_remaining, 2);

            if ($returned <= 0) {
                throw new \InvalidArgumentException(__('financial_custody.nothing_to_return'));
            }

            $custody->amount_remaining = 0;
            $custody->status = FinancialCustody::STATUS_CLOSED;
            $custody->save();

            $transaction = FinancialCustodyTransaction::create([
                'financial_custody_id' => $custody->id,
                'action' => FinancialCustodyTransaction::ACTION_RETURN_REMAINING,
                'amount_settled' => $returned,
                'amount_remaining_after' => 0,
                'purchase_description' => null,
                'notes' => $notes !== null && trim($notes) !== '' ? trim($notes) : null,
                'recorded_by' => $recordedBy,
                'recorded_at' => now(),
            ]);

            $this->ledger->syncCustodyReturn($transaction, $custody);

            AuditHelper::log(
                'financial_custody_returned',
                'FinancialCustody',
                $custody->id,
                'إرجاع متبقي عهدة بمبلغ '.$returned.' — عهدة #'.$custody->id,
                $recordedBy
            );

            return $custody->fresh(['employee', 'transactions.recorder']);
        });
    }
}
