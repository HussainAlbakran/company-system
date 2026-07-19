<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class EmployeeAdvancePayment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'employee_advance_id',
        'payroll_register_id',
        'month',
        'year',
        'amount',
        'recorded_by',
        'recorded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'recorded_at' => 'datetime',
        'month' => 'integer',
        'year' => 'integer',
    ];

    protected static function booted(): void
    {
        static::deleting(function (): void {
            throw ValidationException::withMessages([
                'payment' => [__('employee_advance.cannot_delete_payment')],
            ]);
        });
    }

    public function advance(): BelongsTo
    {
        return $this->belongsTo(EmployeeAdvance::class, 'employee_advance_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function payrollRegister(): BelongsTo
    {
        return $this->belongsTo(PayrollRegister::class);
    }
}
