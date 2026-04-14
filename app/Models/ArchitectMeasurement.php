<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchitectMeasurement extends Model
{
    protected $fillable = [
        'project_id',
        'type',
        'name',
        'length',
        'width',
        'height',
        'quantity',
        'unit',
        'area',
        'volume',
        'price', // 🔥 جديد
        'notes',
    ];

    protected $casts = [
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'quantity' => 'integer',
        'area' => 'decimal:2',
        'volume' => 'decimal:2',
        'price' => 'decimal:2', // 🔥 جديد
    ];

    // 🔥 المشروع المرتبط
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // 🔥 حساب تلقائي قبل الحفظ
    protected static function booted()
    {
        static::saving(function ($measurement) {
            $length = (float) ($measurement->length ?? 0);
            $width = (float) ($measurement->width ?? 0);
            $height = (float) ($measurement->height ?? 0);
            $quantity = (int) ($measurement->quantity ?? 1);

            $measurement->area = round($length * $width * $quantity, 2);
            $measurement->volume = round($length * $width * $height * $quantity, 2);
        });
    }
}