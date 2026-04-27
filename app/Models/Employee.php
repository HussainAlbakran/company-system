<?php

namespace App\Models;

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

        // 🔥 الإقامة
        'residency_number',
        'residency_expiry_date',

        // 🔥 الجواز
        'passport_number',
        'passport_expiry_date',

        'leave_balance',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'residency_expiry_date' => 'date',
        'passport_expiry_date' => 'date',
        'salary' => 'decimal:2',
        'leave_balance' => 'integer',
    ];

    // ================= العلاقات =================

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

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function assets()
    {
        return $this->hasMany(EmployeeAsset::class);
    }

    public function activeAssets()
    {
        return $this->hasMany(EmployeeAsset::class)->where('status', 'active');
    }

    public function assetAssignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Projects where this employee is the responsible assignee.
     */
    public function assignedProjects()
    {
        return $this->hasMany(Project::class, 'responsible_employee_id');
    }

    public function getHasCustodyAttribute(): bool
    {
        if (array_key_exists('active_assets_count', $this->attributes)) {
            return (int) $this->attributes['active_assets_count'] > 0;
        }

        return $this->activeAssets()->exists();
    }
}
