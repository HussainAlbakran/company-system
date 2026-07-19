<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_custody_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('financial_custody_transactions', 'purchase_description')) {
                $table->text('purchase_description')->nullable()->after('amount_remaining_after');
            }
        });
    }

    public function down(): void
    {
        Schema::table('financial_custody_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('financial_custody_transactions', 'purchase_description')) {
                $table->dropColumn('purchase_description');
            }
        });
    }
};
