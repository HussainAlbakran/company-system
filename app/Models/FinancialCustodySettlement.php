<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialCustodySettlement extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_APPROVED = 'approved';

    protected $fillable = [
        'financial_custody_id',
        'employee_id',
        'settlement_year',
        'sequence_number',
        'status',
        'settlement_date',
        'total_amount',
        'total_tax',
        'grand_total',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'settlement_date' => 'date',
        'approved_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'settlement_year' => 'integer',
        'sequence_number' => 'integer',
    ];

    public function custody(): BelongsTo
    {
        return $this->belongsTo(FinancialCustody::class, 'financial_custody_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(FinancialCustodyInvoice::class)->orderBy('line_number');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function referenceCode(): ?string
    {
        if ($this->sequence_number === null) {
            return null;
        }

        return sprintf('%02d%03d', (int) $this->settlement_year, (int) $this->sequence_number);
    }

    public function displayTitle(): string
    {
        $code = $this->referenceCode();

        return $code
            ? __('custody_settlement.title_with_code', ['code' => $code])
            : __('custody_settlement.title_draft');
    }

    public function recalculateTotals(): void
    {
        $amount = round((float) $this->invoices()->sum('amount'), 2);
        $tax = round((float) $this->invoices()->sum('tax_amount'), 2);
        $total = round((float) $this->invoices()->sum('total_amount'), 2);

        $this->update([
            'total_amount' => $amount,
            'total_tax' => $tax,
            'grand_total' => $total > 0 ? $total : round($amount + $tax, 2),
        ]);
    }
}
