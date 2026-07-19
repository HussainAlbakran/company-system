<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('employee_advance_payments', 'payroll_register_id')) {
            return;
        }

        $payments = DB::table('employee_advance_payments')
            ->whereNull('payroll_register_id')
            ->orderBy('id')
            ->get(['id', 'month', 'year']);

        foreach ($payments as $payment) {
            $registerId = DB::table('payroll_registers')
                ->where('month', $payment->month)
                ->where('year', $payment->year)
                ->where('status', 'approved')
                ->orderByDesc('id')
                ->value('id');

            if ($registerId) {
                DB::table('employee_advance_payments')
                    ->where('id', $payment->id)
                    ->update(['payroll_register_id' => $registerId]);
            }
        }
    }

    public function down(): void
    {
        // لا يُلغى الربط تلقائياً
    }
};
