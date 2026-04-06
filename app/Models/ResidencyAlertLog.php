<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResidencyAlertLog extends Model
{
    protected $fillable = [
        'employee_id',
        'days_remaining',
        'sent_date',
        'alert_type',
    ];

    protected function casts(): array
    {
        return [
            'sent_date' => 'date',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}