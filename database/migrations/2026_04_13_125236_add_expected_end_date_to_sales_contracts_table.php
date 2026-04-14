<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_contracts', function (Blueprint $table) {
            $table->date('expected_end_date')->nullable()->after('actual_start_date');
        });
    }

    public function down(): void
    {
        Schema::table('sales_contracts', function (Blueprint $table) {
            $table->dropColumn('expected_end_date');
        });
    }
};