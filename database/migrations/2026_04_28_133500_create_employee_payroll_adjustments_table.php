<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_payroll_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('leave_deduction_days', 8, 2)->default(0);
            $table->decimal('other_deduction', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['employee_id', 'month', 'year'], 'employee_payroll_adjustments_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_payroll_adjustments');
    }
};
