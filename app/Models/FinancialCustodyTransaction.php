<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class FinancialCustodyTransaction extends Model
{
    public const ACTION_ISSUE = 'issue';

    public const ACTION_FULL_SETTLEMENT = 'full_settlement';

    public const ACTION_PARTIAL_SETTLEMENT = 'partial_settlement';

    public const ACTION_RETURN_REMAINING = 'return_remaining';

    public const ACTION_CARRYOVER_OUT = 'carryover_out';

    public const ACTION_CARRYOVER_IN = 'carryover_in';

    public $timestamps = false;

    protected $fillable = [
        'financial_custody_id',
        'action',
        'amount_settled',
        'amount_remaining_after',
        'purchase_description',
        'notes',
        'recorded_by',
        'recorded_at',
    ];

    protected $casts = [
        'amount_settled' => 'decimal:2',
        'amount_remaining_after' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleting(function (): void {
            throw ValidationException::withMessages([
                'transaction' => [__('financial_custody.cannot_delete_log')],
            ]);
        });
    }

    public function custody(): BelongsTo
    {
        return $this->belongsTo(FinancialCustody::class, 'financial_custody_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
