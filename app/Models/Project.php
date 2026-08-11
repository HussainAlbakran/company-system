<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'completed_at',
        'project_pdf',
        'completion_letter_path',
        'current_stage',
        'notes',
        'required_concrete_quantity',
        'created_by',
        'updated_by',
        'client_user_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'completed_at' => 'datetime',
        'progress_percentage' => 'decimal:2',
        'project_value' => 'decimal:2',
        'expenses' => 'decimal:2',
        'required_concrete_quantity' => 'decimal:2',
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

    public function clientUser()
    {
        return $this->belongsTo(User::class, 'client_user_id');
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

    public function reports()
    {
        return $this->hasMany(ProjectReport::class);
    }

    /**
     * Next project code for the given year: APC-2026-0001 (resets each year).
     */
    public static function generateNextCode(?int $year = null): string
    {
        $year = $year ?? (int) date('Y');
        $prefix = 'APC-'.$year.'-';

        $lastCode = static::query()
            ->where('project_code', 'like', $prefix.'%')
            ->orderByDesc('project_code')
            ->lockForUpdate()
            ->value('project_code');

        $next = 1;
        if (is_string($lastCode) && preg_match('/^APC-\d{4}-(\d+)$/', $lastCode, $matches)) {
            $next = ((int) $matches[1]) + 1;
        }

        return $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null
            || $this->status === 'completed'
            || $this->current_stage === 'completed';
    }

    public function scopeActive($query)
    {
        return $query->where(function ($inner) {
            $inner->whereNull('completed_at')
                ->where(function ($status) {
                    $status->whereNull('status')->orWhere('status', '!=', 'completed');
                })
                ->where(function ($stage) {
                    $stage->whereNull('current_stage')->orWhere('current_stage', '!=', 'completed');
                });
        });
    }

    public function scopeArchived($query)
    {
        return $query->where(function ($inner) {
            $inner->whereNotNull('completed_at')
                ->orWhere('status', 'completed')
                ->orWhere('current_stage', 'completed');
        });
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

    public function architectMaterialRequests()
    {
        return $this->hasMany(ArchitectMaterialRequest::class);
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

    public function installationFactoryRequests()
    {
        return $this->hasMany(InstallationFactoryRequest::class);
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
