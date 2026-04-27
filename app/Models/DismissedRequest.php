<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DismissedRequest extends Model
{
    protected $fillable = [
        'user_id',
        'item_key',
        'dismissed_at',
        'hidden_until',
    ];

    protected function casts(): array
    {
        return [
            'dismissed_at' => 'datetime',
            'hidden_until' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
