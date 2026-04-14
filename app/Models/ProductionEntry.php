<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionEntry extends Model
{
    protected $fillable = [
        'production_order_id',
        'project_id', // 🔥 جديد
        'entry_date',
        'quantity',
        'start_time',
        'end_time',
        'working_hours',
        'employee_id',
        'notes',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'quantity' => 'decimal:2',
        'working_hours' => 'decimal:2',
    ];

    // 🔥 علاقة مع أمر الإنتاج
    public function order()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
    }

    // 🔥 علاقة مع المشروع (NEW)
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // 🔥 علاقة مع الموظف
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}