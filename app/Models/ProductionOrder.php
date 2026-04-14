<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    protected $fillable = [
        'project_id',
        'order_number',
        'product_name',
        'planned_quantity',
        'produced_quantity',
        'supplied_quantity',
        'production_start_date',
        'expected_end_date',
        'actual_end_date',
        'daily_target',
        'status',
        'notes',
    ];

    protected $casts = [
        'planned_quantity' => 'decimal:2',
        'produced_quantity' => 'decimal:2',
        'supplied_quantity' => 'decimal:2',
        'daily_target' => 'decimal:2',
        'production_start_date' => 'date',
        'expected_end_date' => 'date',
        'actual_end_date' => 'date',
    ];

    protected $appends = [
        'production_percentage',
        'supply_percentage',
        'remaining_quantity',
        'expected_production_days',
        'remaining_days_to_end',
        'project_display_name',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function entries()
    {
        return $this->hasMany(ProductionEntry::class);
    }

    public function supplies()
    {
        return $this->hasMany(ProductionSupply::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getProductionPercentageAttribute(): float
    {
        $planned = (float) $this->planned_quantity;
        $produced = (float) $this->produced_quantity;

        if ($planned <= 0) {
            return 0;
        }

        return round(min(($produced / $planned) * 100, 100), 2);
    }

    public function getSupplyPercentageAttribute(): float
    {
        $planned = (float) $this->planned_quantity;
        $supplied = (float) $this->supplied_quantity;

        if ($planned <= 0) {
            return 0;
        }

        return round(min(($supplied / $planned) * 100, 100), 2);
    }

    public function getRemainingQuantityAttribute(): float
    {
        return round(max((float) $this->planned_quantity - (float) $this->produced_quantity, 0), 2);
    }

    public function getExpectedProductionDaysAttribute(): ?int
    {
        $averageDailyProduction = $this->entries()
            ->selectRaw('COALESCE(AVG(quantity), 0) as avg_qty')
            ->value('avg_qty');

        if (!$averageDailyProduction || $averageDailyProduction <= 0) {
            return null;
        }

        $remaining = max((float) $this->planned_quantity - (float) $this->produced_quantity, 0);

        return (int) ceil($remaining / $averageDailyProduction);
    }

    public function getRemainingDaysToEndAttribute(): ?int
    {
        if (!$this->expected_end_date) {
            return null;
        }

        $today = Carbon::today();
        $endDate = Carbon::parse($this->expected_end_date);

        return (int) $today->diffInDays($endDate, false);
    }

    public function getProjectDisplayNameAttribute(): string
    {
        if (!$this->project) {
            return 'غير مربوط بمشروع';
        }

        $code = $this->project->project_code ?? '-';
        $name = $this->project->name ?? '-';

        return $code . ' - ' . $name;
    }
}