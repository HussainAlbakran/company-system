<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_assets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            // 🔥 اسم الأصل
            $table->string('asset_name');

            // 🔥 الرقم التسلسلي (المهم)
            $table->string('serial_number');

            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->enum('status', ['active', 'ended', 'lost', 'damaged'])->default('active');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_assets');
    }
};