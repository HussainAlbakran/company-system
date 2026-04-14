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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('residency_number')->nullable()->after('user_id');
            $table->string('passport_number')->nullable()->after('residency_expiry_date');
            $table->date('passport_expiry_date')->nullable()->after('passport_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'residency_number',
                'passport_number',
                'passport_expiry_date',
            ]);
        });
    }
};