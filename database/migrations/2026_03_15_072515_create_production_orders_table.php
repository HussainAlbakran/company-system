<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number')->unique();
            $table->string('product_name');

            $table->decimal('planned_quantity', 12, 2)->default(0);
            $table->decimal('produced_quantity', 12, 2)->default(0);
            $table->decimal('supplied_quantity', 12, 2)->default(0);

            $table->date('production_start_date')->nullable();
            $table->date('expected_end_date')->nullable();
            $table->date('actual_end_date')->nullable();

            $table->decimal('daily_target', 12, 2)->nullable();

            $table->enum('status', [
                'pending',
                'in_progress',
                'paused',
                'completed',
                'cancelled'
            ])->default('pending');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_orders');
    }
};