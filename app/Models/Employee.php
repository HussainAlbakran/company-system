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
        'contract_start_date',
        'contract_end_date',
        'salary',
        'housing_allowance',
        'transportation_allowance',
        'travel_allowance',
        'risk_allowance',
        'transfer_allowance',
        'overtime_allowance',
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
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'residency_expiry_date' => 'date',
        'passport_expiry_date' => 'date',
        'salary' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transportation_allowance' => 'decimal:2',
        'travel_allowance' => 'decimal:2',
        'risk_allowance' => 'decimal:2',
        'transfer_allowance' => 'decimal:2',
        'overtime_allowance' => 'decimal:2',
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

    /** عهدة نشطة من وحدة الأصول (تسليم دون إرجاع). */
    public function activeAssetAssignments()
    {
        return $this->hasMany(AssetAssignment::class)
            ->where('status', 'assigned')
            ->whereNull('returned_at');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payrollAdjustments()
    {
        return $this->hasMany(EmployeePayrollAdjustment::class);
    }

    public function financialCustodies()
    {
        return $this->hasMany(FinancialCustody::class);
    }

    public function advances()
    {
        return $this->hasMany(EmployeeAdvance::class);
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
        $hasLegacyCustody = array_key_exists('active_assets_count', $this->attributes)
            ? (int) $this->attributes['active_assets_count'] > 0
            : $this->activeAssets()->exists();

        if ($hasLegacyCustody) {
            return true;
        }

        return array_key_exists('active_asset_assignments_count', $this->attributes)
            ? (int) $this->attributes['active_asset_assignments_count'] > 0
            : $this->activeAssetAssignments()->exists();
    }
}
