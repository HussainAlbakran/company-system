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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            // ربط مع المشتريات
            $table->foreignId('purchase_id')->nullable()->constrained()->nullOnDelete();

            // بيانات الأصل
            $table->string('name'); // اسم الأصل
            $table->integer('quantity')->default(1);
            $table->string('serial_number')->nullable(); // رقم تسلسلي

            $table->date('purchase_date')->nullable();

            // حالة الأصل
            $table->enum('status', ['available', 'assigned', 'maintenance'])
                  ->default('available');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};