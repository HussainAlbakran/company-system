<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_custodies', function (Blueprint $table) {
            $table->decimal('carried_over_amount', 14, 2)->default(0)->after('amount_remaining');
        });
    }

    public function down(): void
    {
        Schema::table('financial_custodies', function (Blueprint $table) {
            $table->dropColumn('carried_over_amount');
        });
    }
};
