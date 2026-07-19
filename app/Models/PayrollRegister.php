<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'month' => 'integer',
            'year' => 'integer',
        ];
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function advancePayments()
    {
        return $this->hasMany(EmployeeAdvancePayment::class)->orderBy('id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function periodLabel(): string
    {
        return $this->month.'/'.$this->year;
    }

    /**
     * @return array{month: int, year: int}
     */
    public static function nextPeriodAfter(int $month, int $year): array
    {
        $date = \Carbon\Carbon::create($year, $month, 1)->addMonth();

        return [
            'month' => (int) $date->month,
            'year' => (int) $date->year,
        ];
    }
}
