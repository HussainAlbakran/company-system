<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'responsible_employee_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'progress_percentage',
        'project_value',
        'expenses',
        'status',
        'project_pdf',
        'notes',
        'created_by',
        'updated_by',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function responsibleEmployee()
    {
        return $this->belongsTo(Employee::class, 'responsible_employee_id');
    }

    public function updates()
    {
        return $this->hasMany(ProjectUpdate::class);
    }
}