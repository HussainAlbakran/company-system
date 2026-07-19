<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_custodies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount_issued', 14, 2);
            $table->decimal('amount_remaining', 14, 2);
            $table->string('status', 20)->default('open');
            $table->date('issued_at');
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('financial_custody_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_custody_id')->constrained()->cascadeOnDelete();
            $table->string('action', 30);
            $table->decimal('amount_settled', 14, 2)->default(0);
            $table->decimal('amount_remaining_after', 14, 2);
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('recorded_at');
        });

        Schema::create('employee_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_amount', 14, 2);
            $table->unsignedTinyInteger('installment_count');
            $table->decimal('installment_amount', 14, 2);
            $table->unsignedTinyInteger('installments_paid')->default(0);
            $table->string('status', 20)->default('active');
            $table->unsignedTinyInteger('start_month');
            $table->unsignedSmallInteger('start_year');
            $table->date('issued_at');
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_advance_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_advance_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->decimal('amount', 14, 2);
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('recorded_at');
            $table->unique(['employee_advance_id', 'month', 'year'], 'advance_payment_month_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_advance_payments');
        Schema::dropIfExists('employee_advances');
        Schema::dropIfExists('financial_custody_transactions');
        Schema::dropIfExists('financial_custodies');
    }
};
