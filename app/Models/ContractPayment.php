<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContractPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_contract_id',
        'payment_type',
        'amount',
        'payment_date',
        'notes',
    ];

    /**
     * العقد المرتبط
     */
    public function contract()
    {
        return $this->belongsTo(SalesContract::class, 'sales_contract_id');
    }
}