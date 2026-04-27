<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchitectMaterialRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'material_name',
        'description',
        'quantity',
        'unit',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
        ];
    }

    public function request()
    {
        return $this->belongsTo(ArchitectMaterialRequest::class, 'request_id');
    }
}
