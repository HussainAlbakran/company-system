<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class FinancialCustody extends Model
{
    public const STATUS_OPEN = 'open';

    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'employee_id',
        'amount_issued',
        'amount_remaining',
        'carried_over_amount',
        'status',
        'issued_at',
        'issued_by',
        'notes',
    ];

    protected $casts = [
        'amount_issued' => 'decimal:2',
        'amount_remaining' => 'decimal:2',
        'carried_over_amount' => 'decimal:2',
        'issued_at' => 'date',
    ];

    protected static function booted(): void
    {
        static::deleting(function (): void {
            throw ValidationException::withMessages([
                'financial_custody' => [__('financial_custody.cannot_delete')],
            ]);
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FinancialCustodyTransaction::class)->orderBy('recorded_at');
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(FinancialCustodySettlement::class)->orderByDesc('id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(FinancialCustodyInvoice::class)->orderByDesc('invoice_date');
    }

    public function draftSettlement(): ?FinancialCustodySettlement
    {
        return $this->settlements()->where('status', FinancialCustodySettlement::STATUS_DRAFT)->latest('id')->first();
    }

    public function newCashAmount(): float
    {
        return round(max(0, (float) $this->amount_issued - (float) $this->carried_over_amount), 2);
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public static function openForEmployee(int $employeeId): ?self
    {
        return static::query()
            ->where('employee_id', $employeeId)
            ->where('status', self::STATUS_OPEN)
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->first();
    }

    public static function hasOpenForEmployee(int $employeeId): bool
    {
        return static::query()
            ->where('employee_id', $employeeId)
            ->where('status', self::STATUS_OPEN)
            ->exists();
    }

    public function totalSpent(): float
    {
        return round((float) $this->transactions()
            ->whereIn('action', [
                FinancialCustodyTransaction::ACTION_FULL_SETTLEMENT,
                FinancialCustodyTransaction::ACTION_PARTIAL_SETTLEMENT,
            ])
            ->sum('amount_settled'), 2);
    }

    public function totalReturned(): float
    {
        return round((float) $this->transactions()
            ->where('action', FinancialCustodyTransaction::ACTION_RETURN_REMAINING)
            ->sum('amount_settled'), 2);
    }
}
