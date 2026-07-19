<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetMaintenanceLog extends Model
{
    protected $fillable = [
        'asset_id',
        'asset_name',
        'serial_number',
        'asset_type',
        'plate_number',
        'quantity',
        'maintenance_cost',
        'maintenance_date',
        'ended_at',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'ended_at' => 'datetime',
        'maintenance_cost' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function isOpen(): bool
    {
        return $this->ended_at === null;
    }
}
