<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'purchase_id',
        'name',
        'asset_type',
        'quantity',
        'serial_number',
        'plate_number',
        'color',
        'vehicle_type',
        'inspection_expiry_date',
        'registration_number',
        'registration_expiry_date',
        'purchase_date',
        'notes',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'purchase_date' => 'date',
        'inspection_expiry_date' => 'date',
        'registration_expiry_date' => 'date',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function assignments()
    {
        return $this->hasMany(EmployeeAsset::class, 'asset_name', 'name');
    }

    public function assetAssignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function currentActiveAssignment()
    {
        return $this->hasOne(AssetAssignment::class)
            ->where('status', 'assigned')
            ->whereNull('returned_at');
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(AssetMaintenanceLog::class)->latest('maintenance_date');
    }
}