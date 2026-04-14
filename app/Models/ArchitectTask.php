<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchitectTask extends Model
{
    protected $fillable = [
        'project_id',
        'drawing_type',
        'drawing_status',
        'planning_status',
        'drawing_file',
        'planning_file',
        'notes',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // 🔥 المشروع المرتبط
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // 🔥 المعتمد
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}