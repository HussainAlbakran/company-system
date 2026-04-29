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
}
