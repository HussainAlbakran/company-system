<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();

            // 🔥 ربط بالمشروع
            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();

            // 🔥 نوع العملية (شراء / إصلاح)
            $table->enum('type', ['purchase', 'repair'])->default('purchase');

            // 🔥 اسم البند (يدوي)
            $table->string('title');

            // 🔥 الوصف
            $table->text('description')->nullable();

            // 🔥 التكلفة
            $table->decimal('cost', 12, 2)->default(0);

            // 🔥 المورد / الجهة
            $table->string('vendor')->nullable();

            // 🔥 تاريخ العملية
            $table->date('purchase_date')->nullable();

            // 🔥 ملاحظات
            $table->text('notes')->nullable();

            // 🔥 من أنشأ العملية
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};