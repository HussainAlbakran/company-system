<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_advances', function (Blueprint $table) {
            if (! Schema::hasColumn('employee_advances', 'repayment_delay_months')) {
                $table->unsignedTinyInteger('repayment_delay_months')->default(0)->after('start_year');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_advances', function (Blueprint $table) {
            if (Schema::hasColumn('employee_advances', 'repayment_delay_months')) {
                $table->dropColumn('repayment_delay_months');
            }
        });
    }
};
