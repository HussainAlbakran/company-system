<?php

namespace App\Services;

use App\Helpers\AuditHelper;
use App\Models\FinancialCustody;
use App\Models\FinancialCustodyInvoice;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CustodyInvoiceService
{
    public const VAT_RATE = 0.15;

    /** @var list<string> */
    public const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
        'application/pdf',
    ];

    public function registerForEmployee(User $user, array $data, ?UploadedFile $file): FinancialCustodyInvoice
    {
        $employee = $user->employee;
        if (! $employee) {
            throw ValidationException::withMessages([
                'employee' => [__('custody_invoice.no_employee_profile')],
            ]);
        }

        $totals = $this->normalizeTotals($data);

        return DB::transaction(function () use ($employee, $data, $file, $totals, $user) {
            $custody = FinancialCustody::query()
                ->where('employee_id', $employee->id)
                ->where('status', FinancialCustody::STATUS_OPEN)
                ->orderByDesc('issued_at')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if (! $custody) {
                throw ValidationException::withMessages([
                    'custody' => [__('custody_invoice.no_open_custody')],
                ]);
            }

            $this->assertTotalWithinAvailable($custody, $totals['total_amount']);

            $invoice = FinancialCustodyInvoice::create([
                'financial_custody_id' => $custody->id,
                'employee_id' => $employee->id,
                'line_number' => 0,
                'invoice_date' => $data['invoice_date'],
                'supplier_name' => trim((string) $data['supplier_name']),
                'supplier_tax_number' => isset($data['supplier_tax_number']) && $data['supplier_tax_number'] !== ''
                    ? trim((string) $data['supplier_tax_number'])
                    : null,
                'description' => isset($data['description']) ? trim((string) $data['description']) : null,
                'amount' => $totals['amount'],
                'tax_amount' => $totals['tax_amount'],
                'total_amount' => $totals['total_amount'],
                'status' => FinancialCustodyInvoice::STATUS_REGISTERED,
                'recorded_by' => $user->id,
            ]);

            if ($file) {
                $this->storeAttachment($invoice, $file);
            }

            AuditHelper::log(
                'custody_invoice_registered',
                'FinancialCustodyInvoice',
                $invoice->id,
                'تسجيل فاتورة عهدة #'.$invoice->id.' — '.$invoice->supplier_name,
                (int) $user->id
            );

            return $invoice->fresh();
        });
    }

    public function updateForEmployee(User $user, FinancialCustodyInvoice $invoice, array $data, ?UploadedFile $file): FinancialCustodyInvoice
    {
        if (! $this->userCanEditInvoice($user, $invoice)) {
            throw ValidationException::withMessages([
                'invoice' => [__('custody_invoice.cannot_edit')],
            ]);
        }

        $totals = $this->normalizeTotals($data);

        return DB::transaction(function () use ($user, $invoice, $data, $file, $totals) {
            $invoice = FinancialCustodyInvoice::query()->whereKey($invoice->id)->lockForUpdate()->firstOrFail();

            if (! $this->userCanEditInvoice($user, $invoice)) {
                throw ValidationException::withMessages([
                    'invoice' => [__('custody_invoice.cannot_edit')],
                ]);
            }

            $custody = FinancialCustody::query()
                ->whereKey($invoice->financial_custody_id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $custody->isOpen()) {
                throw ValidationException::withMessages([
                    'custody' => [__('custody_invoice.no_open_custody')],
                ]);
            }

            $this->assertTotalWithinAvailable($custody, $totals['total_amount'], $invoice->id);

            $invoice->update([
                'invoice_date' => $data['invoice_date'],
                'supplier_name' => trim((string) $data['supplier_name']),
                'supplier_tax_number' => isset($data['supplier_tax_number']) && $data['supplier_tax_number'] !== ''
                    ? trim((string) $data['supplier_tax_number'])
                    : null,
                'description' => isset($data['description']) ? trim((string) $data['description']) : null,
                'amount' => $totals['amount'],
                'tax_amount' => $totals['tax_amount'],
                'total_amount' => $totals['total_amount'],
            ]);

            if ($file) {
                $this->storeAttachment($invoice, $file);
            }

            AuditHelper::log(
                'custody_invoice_updated',
                'FinancialCustodyInvoice',
                $invoice->id,
                'تعديل فاتورة عهدة #'.$invoice->id.' — '.$invoice->supplier_name,
                (int) $user->id
            );

            return $invoice->fresh();
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{amount: float, tax_amount: float, total_amount: float}
     */
    protected function normalizeTotals(array $data): array
    {
        $amount = round((float) ($data['amount'] ?? 0), 2);

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => [__('custody_invoice.invalid_amount')],
            ]);
        }

        $tax = self::calculateVat($amount);

        return FinancialCustodyInvoice::syncLineTotals($amount, $tax);
    }

    public static function calculateVat(float $amount): float
    {
        return round(max(0, $amount) * self::VAT_RATE, 2);
    }

    protected function assertTotalWithinAvailable(FinancialCustody $custody, float $totalAmount, ?int $excludeInvoiceId = null): void
    {
        $available = $this->availableForRegistration($custody, $excludeInvoiceId);

        if (round($totalAmount, 2) > $available) {
            throw ValidationException::withMessages([
                'amount' => [__('custody_invoice.exceeds_available', [
                    'max' => number_format($available, 2),
                ])],
            ]);
        }
    }

    public function storeAttachment(FinancialCustodyInvoice $invoice, UploadedFile $file): void
    {
        if (! in_array($file->getMimeType(), self::ALLOWED_MIMES, true)) {
            throw ValidationException::withMessages([
                'attachment' => [__('custody_invoice.invalid_attachment')],
            ]);
        }

        if ($file->getSize() > 10 * 1024 * 1024) {
            throw ValidationException::withMessages([
                'attachment' => [__('custody_invoice.attachment_too_large')],
            ]);
        }

        if ($invoice->attachment_path && Storage::disk('local')->exists($invoice->attachment_path)) {
            Storage::disk('local')->delete($invoice->attachment_path);
        }

        $path = $file->store(
            'custody_invoices/'.$invoice->financial_custody_id,
            'local'
        );

        $invoice->update([
            'attachment_path' => $path,
            'attachment_original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function userCanViewInvoice(User $user, FinancialCustodyInvoice $invoice): bool
    {
        if ($user->canAccessCashFlowModule()) {
            return true;
        }

        return $user->employee
            && (int) $user->employee->id === (int) $invoice->employee_id;
    }

    public function userCanEditInvoice(User $user, FinancialCustodyInvoice $invoice): bool
    {
        if (! $user->employee || (int) $user->employee->id !== (int) $invoice->employee_id) {
            return false;
        }

        if ($invoice->status !== FinancialCustodyInvoice::STATUS_REGISTERED) {
            return false;
        }

        if ($invoice->financial_custody_settlement_id !== null) {
            return false;
        }

        $custody = $invoice->relationLoaded('custody')
            ? $invoice->custody
            : $invoice->custody()->first();

        return $custody && $custody->isOpen();
    }

    public function pendingInvoicesTotal(FinancialCustody $custody, ?int $excludeInvoiceId = null): float
    {
        $query = FinancialCustodyInvoice::query()
            ->where('financial_custody_id', $custody->id)
            ->whereIn('status', [
                FinancialCustodyInvoice::STATUS_REGISTERED,
                FinancialCustodyInvoice::STATUS_ON_SETTLEMENT,
            ]);

        if ($excludeInvoiceId) {
            $query->where('id', '!=', $excludeInvoiceId);
        }

        return round((float) $query->sum('total_amount'), 2);
    }

    public function availableForRegistration(FinancialCustody $custody, ?int $excludeInvoiceId = null): float
    {
        $remaining = round((float) $custody->amount_remaining, 2);

        return max(0, round($remaining - $this->pendingInvoicesTotal($custody, $excludeInvoiceId), 2));
    }
}
