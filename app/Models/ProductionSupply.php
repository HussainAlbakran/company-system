<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionSupply extends Model
{
    protected $fillable = [
        'production_order_id',
        'supply_date',
        'quantity',
        'receiver_name',
        'notes',
    ];

    protected $casts = [
        'supply_date' => 'date',
        'quantity' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}