<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_entries', function (Blueprint $table) {
            $table->id();

            // 🔥 ربط بأمر الإنتاج
            $table->foreignId('production_order_id')
                ->constrained('production_orders')
                ->cascadeOnDelete();

            // 🔥 ربط بالمشروع (NEW)
            $table->foreignId('project_id')
                ->nullable()
                ->constrained('projects')
                ->nullOnDelete();

            $table->date('entry_date');

            // 🔥 الكمية المنتجة
            $table->decimal('quantity', 12, 2)->default(0);

            // 🔥 وقت العمل
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            // 🔥 ساعات العمل
            $table->decimal('working_hours', 8, 2)->nullable();

            // 🔥 الموظف
            $table->unsignedBigInteger('employee_id')->nullable();

            // 🔥 ملاحظات
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_entries');
    }
};