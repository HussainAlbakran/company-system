<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class EmployeeAdvance extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_COMPLETED = 'completed';

    public const ALLOWED_INSTALLMENTS = [2, 3, 4, 5, 6];

    public const MAX_REPAYMENT_DELAY_MONTHS = 12;

    protected $fillable = [
        'employee_id',
        'total_amount',
        'base_salary_at_issue',
        'installment_count',
        'installment_amount',
        'installments_paid',
        'status',
        'start_month',
        'start_year',
        'repayment_delay_months',
        'issued_at',
        'issued_by',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'base_salary_at_issue' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'issued_at' => 'date',
        'installment_count' => 'integer',
        'installments_paid' => 'integer',
        'start_month' => 'integer',
        'start_year' => 'integer',
        'repayment_delay_months' => 'integer',
    ];

    protected static function booted(): void
    {
        static::deleting(function (): void {
            throw ValidationException::withMessages([
                'employee_advance' => [__('employee_advance.cannot_delete')],
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

    public function payments(): HasMany
    {
        return $this->hasMany(EmployeeAdvancePayment::class)->orderByDesc('recorded_at');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function remainingInstallments(): int
    {
        return max(0, (int) $this->installment_count - (int) $this->installments_paid);
    }

    public function amountPaidSoFar(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function remainingBalance(): float
    {
        return max(0, round((float) $this->total_amount - $this->amountPaidSoFar(), 2));
    }

    public function repaymentStartLabel(): string
    {
        return sprintf('%02d/%d', (int) $this->start_month, (int) $this->start_year);
    }
}
