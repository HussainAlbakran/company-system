<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_custody_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_custody_id')->constrained(indexName: 'fc_settlements_custody_fk')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained(indexName: 'fc_settlements_employee_fk')->cascadeOnDelete();
            $table->unsignedTinyInteger('settlement_year');
            $table->unsignedSmallInteger('sequence_number')->nullable();
            $table->string('status', 20)->default('draft');
            $table->date('settlement_date')->nullable();
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('total_tax', 14, 2)->default(0);
            $table->decimal('grand_total', 14, 2)->default(0);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['settlement_year', 'sequence_number'], 'custody_settlement_year_seq_unique');
            $table->index(['financial_custody_id', 'status'], 'fc_settlements_custody_status_idx');
        });

        Schema::create('financial_custody_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_custody_id')->constrained(indexName: 'fc_invoices_custody_fk')->cascadeOnDelete();
            $table->unsignedBigInteger('financial_custody_settlement_id')->nullable();
            $table->foreignId('employee_id')->constrained(indexName: 'fc_invoices_employee_fk')->cascadeOnDelete();
            $table->unsignedTinyInteger('line_number')->default(1);
            $table->date('invoice_date');
            $table->string('supplier_name');
            $table->string('supplier_tax_number', 50)->nullable();
            $table->string('classification', 120)->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->string('attachment_path')->nullable();
            $table->string('attachment_original_name')->nullable();
            $table->string('status', 20)->default('registered');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['financial_custody_id', 'status'], 'fc_invoices_custody_status_idx');
            $table->index(['financial_custody_settlement_id', 'line_number'], 'fc_invoices_settlement_line_idx');

            $table->foreign('financial_custody_settlement_id', 'fc_invoices_settlement_fk')
                ->references('id')
                ->on('financial_custody_settlements')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_custody_invoices');
        Schema::dropIfExists('financial_custody_settlements');
    }
};
