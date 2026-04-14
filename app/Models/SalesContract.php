<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'contract_no',
        'contract_date',
        'client_name',
        'main_contractor',
        'project_name',
        'project_location',
        'project_value',
        'project_duration',
        'expected_start_date',
        'actual_start_date',
        'description',
        'notes',
        'contract_file',

        // الدفع
        'payment_type',
        'full_payment_amount',
        'first_payment_title',
        'first_payment_percentage',
        'first_payment_amount',
        'first_payment_due_date',

        'status',
        'created_by',
    ];

    /**
     * المشروع المرتبط بالعقد
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * المستخدم الذي أنشأ العقد
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * الدفعات المرتبطة بالعقد
     */
    public function payments()
    {
        return $this->hasMany(ContractPayment::class, 'sales_contract_id');
    }

    /**
     * هل تم تسجيل أول دفعة؟
     */
    public function hasFirstPayment()
    {
        return $this->payments()->exists();
    }

    /**
     * إجمالي المدفوع
     */
    public function getTotalPaidAttribute()
    {
        return (float) $this->payments()->sum('amount');
    }

    /**
     * المتبقي
     */
    public function getRemainingAmountAttribute()
    {
        return max(0, ((float) $this->project_value) - $this->total_paid);
    }

    /**
     * هل تم سداد كامل قيمة العقد؟
     */
    public function isFullyPaid()
    {
        if (!$this->project_value) {
            return false;
        }

        return $this->total_paid >= (float) $this->project_value;
    }
}