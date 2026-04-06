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

            $table->foreignId('production_order_id')
                ->constrained('production_orders')
                ->cascadeOnDelete();

            $table->date('entry_date');
            $table->decimal('quantity', 12, 2)->default(0);

            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->decimal('working_hours', 8, 2)->nullable();

            $table->unsignedBigInteger('employee_id')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_entries');
    }
};