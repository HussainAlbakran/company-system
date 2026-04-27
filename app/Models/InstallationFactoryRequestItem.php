<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstallationFactoryRequestItem extends Model
{
    protected $fillable = [
        'request_id',
        'item_name',
        'description',
        'quantity',
        'unit',
        'reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(InstallationFactoryRequest::class, 'request_id');
    }
}
