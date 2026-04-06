<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residency_alert_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->integer('days_remaining');
            $table->date('sent_date');
            $table->string('alert_type')->default('email');
            $table->timestamps();

            $table->unique(['employee_id', 'days_remaining', 'sent_date', 'alert_type'], 'residency_alert_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residency_alert_logs');
    }
};