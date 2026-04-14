<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('architect_measurements', function (Blueprint $table) {
            $table->id();

            // 🔥 ربط بالمشروع
            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();

            // 🔥 نوع العنصر (غرفة / جدار / باب / سقف ...)
            $table->string('type')->nullable();

            // 🔥 اسم العنصر
            $table->string('name');

            // 🔥 الأبعاد
            $table->decimal('length', 10, 2)->nullable();   // الطول
            $table->decimal('width', 10, 2)->nullable();    // العرض
            $table->decimal('height', 10, 2)->nullable();   // الارتفاع

            // 🔥 العدد
            $table->integer('quantity')->default(1);

            // 🔥 النتائج المحسوبة
            $table->decimal('area', 12, 2)->default(0);   // المساحة
            $table->decimal('volume', 12, 2)->default(0); // الحجم

            // 🔥 وحدة القياس (متر / سم)
            $table->string('unit')->default('m');

            // 🔥 هل تم إرساله للمصنع
            $table->boolean('sent_to_factory')->default(false);

            // 🔥 هل تم استلامه في التركيبات
            $table->boolean('received_in_installation')->default(false);

            // 🔥 ملاحظات
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('architect_measurements');
    }
};