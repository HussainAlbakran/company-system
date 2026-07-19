<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class FinancialCustodyInvoice extends Model
{
    public const STATUS_REGISTERED = 'registered';

    public const STATUS_ON_SETTLEMENT = 'on_settlement';

    public const STATUS_APPROVED = 'approved';

    protected $fillable = [
        'financial_custody_id',
        'financial_custody_settlement_id',
        'employee_id',
        'line_number',
        'invoice_date',
        'supplier_name',
        'supplier_tax_number',
        'classification',
        'description',
        'amount',
        'tax_amount',
        'total_amount',
        'attachment_path',
        'attachment_original_name',
        'status',
        'recorded_by',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'line_number' => 'integer',
    ];

    public function custody(): BelongsTo
    {
        return $this->belongsTo(FinancialCustody::class, 'financial_custody_id');
    }

    public function settlement(): BelongsTo
    {
        return $this->belongsTo(FinancialCustodySettlement::class, 'financial_custody_settlement_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function hasAttachment(): bool
    {
        return $this->attachment_path
            && Storage::disk('local')->exists($this->attachment_path);
    }

    public static function syncLineTotals(float $amount, float $tax): array
    {
        $amount = round(max(0, $amount), 2);
        $tax = round(max(0, $tax), 2);

        return [
            'amount' => $amount,
            'tax_amount' => $tax,
            'total_amount' => round($amount + $tax, 2),
        ];
    }
}
