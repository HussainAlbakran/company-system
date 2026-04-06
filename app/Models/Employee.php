<?php

namespace App\Models;

use App\Models\Factory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'employee_number',
        'job_title',
        'phone',
        'email',
        'address',
        'hire_date',
        'salary',
        'status',
        'department_id',
        'factory_id',
        'manager_id',
        'user_id',
        'residency_expiry_date',
    ];

    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function factory()
    {
        return $this->belongsTo(Factory::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }
}