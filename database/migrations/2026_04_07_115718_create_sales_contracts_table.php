<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales_contracts', function (Blueprint $table) {
            $table->id();

            // ربط المشروع
            $table->unsignedBigInteger('project_id')->nullable();

            // بيانات العقد
            $table->string('contract_no')->unique();
            $table->date('contract_date');

            $table->string('client_name');
            $table->string('main_contractor')->nullable();

            $table->string('project_name');
            $table->string('project_location')->nullable();

            $table->decimal('project_value', 15, 2)->nullable();
            $table->integer('project_duration')->nullable(); // بالأيام

            $table->date('expected_start_date')->nullable();
            $table->date('actual_start_date')->nullable();

            $table->text('description')->nullable();
            $table->text('notes')->nullable();

            // ملف العقد
            $table->string('contract_file')->nullable();

            // حالة العقد
            $table->enum('status', [
                'draft',
                'approved',
                'rejected'
            ])->default('draft');

            // من أنشأه
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_contracts');
    }
};