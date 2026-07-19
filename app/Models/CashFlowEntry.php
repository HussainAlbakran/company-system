<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashFlowEntry extends Model
{
    public const TYPE_INCOME = 'income';

    public const TYPE_EXPENSE = 'expense';

    /** إرجاع عهدة أو حركة لا تُحسب مدخولاً ولا صرفاً */
    public const TYPE_NEUTRAL = 'neutral';

    public const SOURCE_FINANCIAL_CUSTODY_RETURN = 'financial_custody_return';

    public const SOURCE_ADVANCE_PAYMENT = 'advance_payment';

    public const SOURCE_MANUAL = 'manual';

    public const SOURCE_ASSET_MAINTENANCE = 'asset_maintenance';

    public const SOURCE_PAYROLL_REGISTER = 'payroll_register';

    public const SOURCE_CONTRACT_PAYMENT = 'contract_payment';

    public const SOURCE_PURCHASE = 'purchase';

    public const SOURCE_FINANCIAL_CUSTODY = 'financial_custody';

    public const SOURCE_EMPLOYEE_ADVANCE = 'employee_advance';

    public const CATEGORY_MAINTENANCE = 'صيانة';

    public const CATEGORY_PAYROLL = 'مسير رواتب';

    public const CATEGORY_CONTRACTS = 'عقود';

    public const CATEGORY_PURCHASES = 'مشتريات';

    public const CATEGORY_MANUAL = 'يدوي';

    public const CATEGORY_FINANCIAL_CUSTODY = 'عهدة مالية';

    public const CATEGORY_ADVANCE = 'سلفة';

    public const CATEGORY_ADVANCE_REPAYMENT = 'سداد سلفة';

    protected $fillable = [
        'type',
        'title',
        'category',
        'amount',
        'entry_date',
        'notes',
        'source_type',
        'source_id',
        'recorded_by',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function isIncome(): bool
    {
        return $this->type === self::TYPE_INCOME;
    }

    public function isExpense(): bool
    {
        return $this->type === self::TYPE_EXPENSE;
    }

    public function isNeutral(): bool
    {
        return $this->type === self::TYPE_NEUTRAL;
    }

    public function typeLabel(): string
    {
        if ($this->isNeutral()) {
            if ($this->source_type === self::SOURCE_ADVANCE_PAYMENT) {
                return __('cash_flow.type_advance_repayment');
            }

            if ($this->source_type === self::SOURCE_FINANCIAL_CUSTODY_RETURN) {
                return __('cash_flow.type_custody_return');
            }

            return __('cash_flow.type_neutral');
        }

        return $this->isIncome()
            ? __('cash_flow.type_income')
            : __('cash_flow.type_expense');
    }

    public function signedAmountPrefix(): string
    {
        if ($this->isNeutral()) {
            return '↩ ';
        }

        return $this->isIncome() ? '+' : '-';
    }

    public function isManual(): bool
    {
        return $this->source_type === self::SOURCE_MANUAL
            || $this->source_type === null
            || $this->source_type === '';
    }

    public function isAuto(): bool
    {
        return ! $this->isManual();
    }
}
