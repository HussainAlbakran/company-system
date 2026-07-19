<?php

namespace App\Services;

use App\Helpers\AuditHelper;
use App\Models\FinancialCustody;
use App\Models\FinancialCustodyInvoice;
use App\Models\FinancialCustodySettlement;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CustodySettlementService
{
    public function __construct(
        protected FinancialCustodyService $custodyService,
        protected CustodyInvoiceService $invoiceService
    ) {}

    public function openOrCreateDraft(FinancialCustody $custody, int $userId): FinancialCustodySettlement
    {
        $existing = $custody->draftSettlement();
        if ($existing) {
            $this->attachPendingInvoices($existing);

            return $existing->fresh(['employee', 'invoices', 'custody']);
        }

        return DB::transaction(function () use ($custody, $userId) {
            $settlement = FinancialCustodySettlement::create([
                'financial_custody_id' => $custody->id,
                'employee_id' => $custody->employee_id,
                'settlement_year' => (int) now()->format('y'),
                'sequence_number' => null,
                'status' => FinancialCustodySettlement::STATUS_DRAFT,
                'settlement_date' => now()->toDateString(),
                'created_by' => $userId,
            ]);

            $this->attachPendingInvoices($settlement);

            return $settlement->fresh(['employee', 'invoices', 'custody']);
        });
    }

    public function attachPendingInvoices(FinancialCustodySettlement $settlement): void
    {
        if (! $settlement->isDraft()) {
            return;
        }

        $pending = FinancialCustodyInvoice::query()
            ->where('financial_custody_id', $settlement->financial_custody_id)
            ->where('status', FinancialCustodyInvoice::STATUS_REGISTERED)
            ->whereNull('financial_custody_settlement_id')
            ->orderBy('invoice_date')
            ->get();

        $nextLine = (int) $settlement->invoices()->max('line_number');

        foreach ($pending as $invoice) {
            $nextLine++;
            $invoice->update([
                'financial_custody_settlement_id' => $settlement->id,
                'line_number' => $nextLine,
                'status' => FinancialCustodyInvoice::STATUS_ON_SETTLEMENT,
            ]);
        }

        $settlement->recalculateTotals();
    }

    /**
     * يدمج فواتير التصفية غير الموجودة في النموذج (مثلاً أُضيفت بعد فتح الصفحة).
     *
     * @param  array<int, array<string, mixed>>  $lines
     * @return array<int, array<string, mixed>>
     */
    public function mergeMissingInvoicesIntoLines(FinancialCustodySettlement $settlement, array $lines): array
    {
        if (! $settlement->isDraft()) {
            return $lines;
        }

        $this->attachPendingInvoices($settlement);
        $settlement->load('invoices');

        $submittedIds = collect($lines)
            ->pluck('id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($settlement->invoices->sortBy('line_number') as $invoice) {
            if (in_array((int) $invoice->id, $submittedIds, true)) {
                continue;
            }

            $date = $invoice->invoice_date ?? now();
            $lines[] = [
                'id' => $invoice->id,
                'invoice_day' => $date->day,
                'invoice_month' => $date->month,
                'invoice_year' => $date->year,
                'supplier_name' => $invoice->supplier_name,
                'supplier_tax_number' => $invoice->supplier_tax_number,
                'classification' => $invoice->classification,
                'description' => $invoice->description,
                'amount' => (float) $invoice->amount,
                'tax_amount' => (float) $invoice->tax_amount,
            ];
        }

        return array_values($lines);
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     */
    public function updateDraft(FinancialCustodySettlement $settlement, array $lines, ?string $settlementDate, int $userId): FinancialCustodySettlement
    {
        if (! $settlement->isDraft()) {
            throw new \InvalidArgumentException(__('custody_settlement.cannot_edit_approved'));
        }

        return DB::transaction(function () use ($settlement, $lines, $settlementDate, $userId) {
            $settlement = FinancialCustodySettlement::query()->whereKey($settlement->id)->lockForUpdate()->firstOrFail();

            if ($settlementDate) {
                $settlement->settlement_date = $settlementDate;
                $settlement->save();
            }

            $existingIds = $settlement->invoices()->pluck('id')->all();
            $keptIds = [];

            foreach ($lines as $index => $line) {
                $lineNumber = $index + 1;
                $amount = round((float) ($line['amount'] ?? 0), 2);
                $tax = round((float) ($line['tax_amount'] ?? 0), 2);
                $totals = FinancialCustodyInvoice::syncLineTotals($amount, $tax);

                $payload = [
                    'line_number' => $lineNumber,
                    'invoice_date' => $line['invoice_date'] ?? now()->toDateString(),
                    'supplier_name' => trim((string) ($line['supplier_name'] ?? '')),
                    'supplier_tax_number' => isset($line['supplier_tax_number']) ? trim((string) $line['supplier_tax_number']) : null,
                    'classification' => isset($line['classification']) ? trim((string) $line['classification']) : null,
                    'description' => isset($line['description']) ? trim((string) $line['description']) : null,
                    'amount' => $totals['amount'],
                    'tax_amount' => $totals['tax_amount'],
                    'total_amount' => $totals['total_amount'],
                ];

                if (! empty($line['id']) && in_array((int) $line['id'], $existingIds, true)) {
                    $invoice = FinancialCustodyInvoice::query()->findOrFail((int) $line['id']);
                    $invoice->update($payload);
                    $keptIds[] = $invoice->id;
                } elseif ($payload['supplier_name'] !== '' || $totals['amount'] > 0) {
                    $invoice = FinancialCustodyInvoice::create(array_merge($payload, [
                        'financial_custody_id' => $settlement->financial_custody_id,
                        'financial_custody_settlement_id' => $settlement->id,
                        'employee_id' => $settlement->employee_id,
                        'status' => FinancialCustodyInvoice::STATUS_ON_SETTLEMENT,
                        'recorded_by' => $userId,
                    ]));
                    $keptIds[] = $invoice->id;
                }
            }

            $settlement->invoices()
                ->whereNotIn('id', $keptIds)
                ->where('status', FinancialCustodyInvoice::STATUS_ON_SETTLEMENT)
                ->get()
                ->each(function (FinancialCustodyInvoice $orphan) {
                    $orphan->update([
                        'financial_custody_settlement_id' => null,
                        'line_number' => 0,
                        'status' => FinancialCustodyInvoice::STATUS_REGISTERED,
                    ]);
                });

            $settlement->recalculateTotals();

            return $settlement->fresh(['employee', 'invoices', 'custody']);
        });
    }

    public function uploadLineAttachment(
        FinancialCustodySettlement $settlement,
        FinancialCustodyInvoice $invoice,
        UploadedFile $file
    ): FinancialCustodyInvoice {
        if (! $settlement->isDraft() || (int) $invoice->financial_custody_settlement_id !== (int) $settlement->id) {
            throw new \InvalidArgumentException(__('custody_settlement.cannot_edit_approved'));
        }

        $this->invoiceService->storeAttachment($invoice, $file);

        return $invoice->fresh();
    }

    public function approve(FinancialCustodySettlement $settlement, int $userId): array
    {
        return DB::transaction(function () use ($settlement, $userId) {
            $settlement = FinancialCustodySettlement::query()->whereKey($settlement->id)->lockForUpdate()->firstOrFail();

            if (! $settlement->isDraft()) {
                throw new \InvalidArgumentException(__('custody_settlement.already_approved'));
            }

            $this->attachPendingInvoices($settlement);
            $settlement->recalculateTotals();
            $grandTotal = round((float) $settlement->grand_total, 2);

            if ($grandTotal <= 0) {
                throw new \InvalidArgumentException(__('custody_settlement.empty_total'));
            }

            $custody = FinancialCustody::query()->whereKey($settlement->financial_custody_id)->lockForUpdate()->firstOrFail();

            if (! $custody->isOpen()) {
                throw new \InvalidArgumentException(__('financial_custody.already_closed'));
            }

            if ($grandTotal > round((float) $custody->amount_remaining, 2)) {
                throw new \InvalidArgumentException(__('custody_settlement.exceeds_remaining', [
                    'max' => number_format((float) $custody->amount_remaining, 2),
                ]));
            }

            $year = (int) ($settlement->settlement_year ?: now()->format('y'));
            $sequence = $this->nextSequenceNumber($year);

            $settlement->update([
                'settlement_year' => $year,
                'sequence_number' => $sequence,
                'status' => FinancialCustodySettlement::STATUS_APPROVED,
                'approved_at' => now(),
                'approved_by' => $userId,
                'settlement_date' => $settlement->settlement_date ?? now()->toDateString(),
            ]);

            $description = $settlement->invoices()
                ->orderBy('line_number')
                ->get()
                ->map(fn ($inv) => $inv->supplier_name.': '.($inv->description ?? $inv->supplier_name))
                ->implode(' | ');

            $custody = $this->custodyService->settlePartial(
                $custody,
                $grandTotal,
                $description !== '' ? $description : __('custody_settlement.default_purchase_desc', ['code' => $settlement->referenceCode()]),
                __('custody_settlement.approval_note', ['code' => $settlement->referenceCode()]),
                $userId
            );

            $remainingToTransfer = round((float) $custody->amount_remaining, 2);
            $carryoverTarget = $this->custodyService->tryCarryOverToNewerCustody($custody, $userId);

            $settlement->invoices()->update(['status' => FinancialCustodyInvoice::STATUS_APPROVED]);

            AuditHelper::log(
                'custody_settlement_approved',
                'FinancialCustodySettlement',
                $settlement->id,
                'اعتماد تصفية عهدة '.$settlement->referenceCode().' — '.$grandTotal,
                $userId
            );

            return [
                'settlement' => $settlement->fresh(['employee', 'invoices', 'custody', 'approver']),
                'carryover_target' => $carryoverTarget,
                'carryover_amount' => $carryoverTarget ? $remainingToTransfer : 0.0,
            ];
        });
    }

    public function nextSequenceNumber(int $year): int
    {
        $max = FinancialCustodySettlement::query()
            ->where('settlement_year', $year)
            ->whereNotNull('sequence_number')
            ->max('sequence_number');

        return ((int) $max) + 1;
    }
}
