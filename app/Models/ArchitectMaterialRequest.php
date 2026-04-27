<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchitectMaterialRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'created_by',
        'status',
        'notes',
        'rejection_reason',
        'attachment_path',
        'submitted_at',
        'approved_at',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(ArchitectMaterialRequestItem::class, 'request_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'architect_material_request_id');
    }
}
