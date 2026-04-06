<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectUpdate extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'progress',
        'attachment',
        'created_by',
        'extracted_text',
        'extracted_numbers_json',
        'ai_summary',
        'processing_status',
    ];

    protected function casts(): array
    {
        return [
            'extracted_numbers_json' => 'array',
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
}