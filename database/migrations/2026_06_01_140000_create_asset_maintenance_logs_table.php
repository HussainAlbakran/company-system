<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('asset_name');
            $table->string('serial_number')->nullable();
            $table->string('asset_type')->nullable();
            $table->string('plate_number')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('maintenance_cost', 12, 2);
            $table->date('maintenance_date');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_maintenance_logs');
    }
};
