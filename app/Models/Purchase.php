<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'project_id',
        'type',
        'title',
        'description',
        'quantity',
        'cost',
        'vendor',
        'purchase_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'purchase_date' => 'date',
    ];

    protected $appends = [
        'type_label',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'purchase' => 'شراء',
            'repair' => 'إصلاح',
            'contract_purchase' => 'مشتريات عقد',
            'asset_purchase' => 'شراء أصول',
            'general_maintenance' => 'صيانة عامة',
            default => 'غير محدد',
        };
    }
}