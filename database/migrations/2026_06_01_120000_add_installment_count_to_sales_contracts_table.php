<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_contracts', function (Blueprint $table) {
            $table->unsignedTinyInteger('installment_count')->nullable()->after('payment_type');
        });

        DB::table('sales_contracts')
            ->where('payment_type', 'installments')
            ->update([
                'payment_type' => 'installments_2',
                'installment_count' => 2,
            ]);

        DB::table('sales_contracts')
            ->where('payment_type', 'full')
            ->whereNull('installment_count')
            ->update(['installment_count' => 1]);
    }

    public function down(): void
    {
        Schema::table('sales_contracts', function (Blueprint $table) {
            $table->dropColumn('installment_count');
        });
    }
};
