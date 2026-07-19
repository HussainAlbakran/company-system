<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_advance_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('employee_advance_payments', 'payroll_register_id')) {
                $table->foreignId('payroll_register_id')
                    ->nullable()
                    ->after('employee_advance_id')
                    ->constrained('payroll_registers')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_advance_payments', function (Blueprint $table) {
            if (Schema::hasColumn('employee_advance_payments', 'payroll_register_id')) {
                $table->dropConstrainedForeignId('payroll_register_id');
            }
        });
    }
};
