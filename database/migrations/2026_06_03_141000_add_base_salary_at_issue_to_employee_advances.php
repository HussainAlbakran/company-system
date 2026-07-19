<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_advances', function (Blueprint $table) {
            if (! Schema::hasColumn('employee_advances', 'base_salary_at_issue')) {
                $table->decimal('base_salary_at_issue', 14, 2)->default(0)->after('total_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_advances', function (Blueprint $table) {
            if (Schema::hasColumn('employee_advances', 'base_salary_at_issue')) {
                $table->dropColumn('base_salary_at_issue');
            }
        });
    }
};
