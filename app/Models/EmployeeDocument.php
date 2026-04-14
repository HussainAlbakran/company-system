<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    protected $fillable = [
        'employee_id',
        'document_type',
        'title',
        'file_path',
        'file_name',
        'file_size',
        'uploaded_by',
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

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}