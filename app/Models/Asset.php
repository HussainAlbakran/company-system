<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'purchase_id',
        'name',
        'quantity',
        'serial_number',
        'purchase_date',
        'notes',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'purchase_date' => 'date',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function assignments()
    {
        return $this->hasMany(EmployeeAsset::class, 'asset_name', 'name');
    }
}