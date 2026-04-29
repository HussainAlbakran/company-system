<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'housing_allowance')) {
                $table->decimal('housing_allowance', 12, 2)->default(0)->nullable()->after('salary');
            }

            if (! Schema::hasColumn('employees', 'transportation_allowance')) {
                $table->decimal('transportation_allowance', 12, 2)->default(0)->nullable()->after('housing_allowance');
            }

            if (! Schema::hasColumn('employees', 'travel_allowance')) {
                $table->decimal('travel_allowance', 12, 2)->default(0)->nullable()->after('transportation_allowance');
            }

            if (! Schema::hasColumn('employees', 'risk_allowance')) {
                $table->decimal('risk_allowance', 12, 2)->default(0)->nullable()->after('travel_allowance');
            }

            if (! Schema::hasColumn('employees', 'transfer_allowance')) {
                $table->decimal('transfer_allowance', 12, 2)->default(0)->nullable()->after('risk_allowance');
            }

            if (! Schema::hasColumn('employees', 'overtime_allowance')) {
                $table->decimal('overtime_allowance', 12, 2)->default(0)->nullable()->after('transfer_allowance');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $columns = [
                'housing_allowance',
                'transportation_allowance',
                'travel_allowance',
                'risk_allowance',
                'transfer_allowance',
                'overtime_allowance',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
