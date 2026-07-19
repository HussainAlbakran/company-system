<?php

namespace App\Models;

use App\Support\ContractPaymentTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'contract_no',
        'contract_date',
        'client_name',
        'main_contractor',
        'project_name',
        'project_location',
        'project_value',
        'project_duration',
        'expected_start_date',
        'actual_start_date',
        'description',
        'notes',
        'contract_file',

        'payment_type',
        'installment_count',
        'full_payment_amount',
        'first_payment_title',
        'first_payment_percentage',
        'first_payment_amount',
        'first_payment_due_date',

        'status',
        'created_by',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(ContractPayment::class, 'sales_contract_id');
    }

    public function isGovernmentPayment(): bool
    {
        return $this->payment_type === ContractPaymentTypes::GOVERNMENT;
    }

    public function isInstallmentPlan(): bool
    {
        return ContractPaymentTypes::isInstallmentType((string) $this->payment_type);
    }

    public function isFullPaymentPlan(): bool
    {
        return $this->payment_type === ContractPaymentTypes::FULL;
    }

    public function resolvedInstallmentCount(): ?int
    {
        if ($this->installment_count) {
            return (int) $this->installment_count;
        }

        return ContractPaymentTypes::installmentCountFor((string) $this->payment_type);
    }

    public function paymentTypeLabel(): string
    {
        return __(ContractPaymentTypes::labelKey((string) $this->payment_type));
    }

    public function installmentShareAmount(): float
    {
        $count = $this->resolvedInstallmentCount();
        $value = (float) ($this->project_value ?? 0);

        if (! $count || $count < 1 || $value <= 0) {
            return 0.0;
        }

        return round($value / $count, 2);
    }

    public function requiresFirstPaymentForDesigns(): bool
    {
        return ! $this->isGovernmentPayment();
    }

    public function hasFirstPayment(): bool
    {
        return $this->payments()->exists();
    }

    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, ((float) $this->project_value) - $this->total_paid);
    }

    public function isFullyPaid(): bool
    {
        if (! $this->project_value) {
            return false;
        }

        return $this->total_paid >= (float) $this->project_value;
    }

    public function syncPaymentStatus(): void
    {
        if ($this->isGovernmentPayment()) {
            $this->update([
                'status' => $this->isFullyPaid() ? 'paid' : 'government',
            ]);

            return;
        }

        $this->update([
            'status' => $this->isFullyPaid() ? 'paid' : ($this->hasFirstPayment() ? 'partial' : 'awaiting_payment'),
        ]);
    }
}
