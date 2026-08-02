<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'insurance_deduction_percent')) {
                $table->decimal('insurance_deduction_percent', 5, 2)
                    ->default(0)
                    ->nullable()
                    ->after('overtime_allowance');
            }
        });

        // Reset payroll cycle to start from August 2026 (pending only — keep approved history).
        if (Schema::hasTable('payroll_registers')) {
            DB::table('payroll_registers')
                ->where('status', 'pending')
                ->delete();

            $exists = DB::table('payroll_registers')
                ->where('month', 8)
                ->where('year', 2026)
                ->exists();

            if (! $exists) {
                DB::table('payroll_registers')->insert([
                    'month' => 8,
                    'year' => 2026,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('payroll_registers')
                    ->where('month', 8)
                    ->where('year', 2026)
                    ->where('status', '!=', 'approved')
                    ->update([
                        'status' => 'pending',
                        'approved_by' => null,
                        'approved_at' => null,
                        'updated_at' => now(),
                    ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'insurance_deduction_percent')) {
                $table->dropColumn('insurance_deduction_percent');
            }
        });
    }
};
