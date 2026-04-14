<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_code',
        'sales_contract_id',
        'department_id',
        'responsible_employee_id',
        'name',
        'client_name',
        'main_contractor',
        'description',
        'start_date',
        'end_date',
        'progress_percentage',
        'project_value',
        'expenses',
        'status',
        'project_pdf',
        'current_stage',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'progress_percentage' => 'decimal:2',
        'project_value' => 'decimal:2',
        'expenses' => 'decimal:2',
    ];

    protected $appends = [
        'total_purchase_cost',
        'total_repair_cost',
        'total_purchase_and_repair_cost',
    ];

    public function salesContract()
    {
        return $this->belongsTo(SalesContract::class, 'sales_contract_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

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

    /*
    |--------------------------------------------------------------------------
    | Architect
    |--------------------------------------------------------------------------
    */
    public function architectTask()
    {
        return $this->hasOne(ArchitectTask::class);
    }

    public function architectMeasurements()
    {
        return $this->hasMany(ArchitectMeasurement::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Factory / Production
    |--------------------------------------------------------------------------
    */
    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class);
    }

    public function productionEntries()
    {
        return $this->hasManyThrough(
            ProductionEntry::class,
            ProductionOrder::class,
            'project_id',           // Foreign key on production_orders table
            'production_order_id',  // Foreign key on production_entries table
            'id',                   // Local key on projects table
            'id'                    // Local key on production_orders table
        );
    }

    public function productionSupplies()
    {
        return $this->hasManyThrough(
            ProductionSupply::class,
            ProductionOrder::class,
            'project_id',
            'production_order_id',
            'id',
            'id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Purchasing
    |--------------------------------------------------------------------------
    */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Totals
    |--------------------------------------------------------------------------
    */
    public function getTotalPurchaseCostAttribute(): float
    {
        return round((float) $this->purchases()->where('type', 'purchase')->sum('cost'), 2);
    }

    public function getTotalRepairCostAttribute(): float
    {
        return round((float) $this->purchases()->where('type', 'repair')->sum('cost'), 2);
    }

    public function getTotalPurchaseAndRepairCostAttribute(): float
    {
        return round(
            (float) $this->purchases()->whereIn('type', ['purchase', 'repair'])->sum('cost'),
            2
        );
    }
}