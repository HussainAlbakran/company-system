<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('assets')) {
            return;
        }

        Schema::table('assets', function (Blueprint $table) {
            if (! Schema::hasColumn('assets', 'registration_number')) {
                $table->string('registration_number')->nullable()->after('inspection_expiry_date');
            }

            if (! Schema::hasColumn('assets', 'registration_expiry_date')) {
                $table->date('registration_expiry_date')->nullable()->after('registration_number');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('assets')) {
            return;
        }

        Schema::table('assets', function (Blueprint $table) {
            $dropColumns = [];
            foreach (['registration_number', 'registration_expiry_date'] as $column) {
                if (Schema::hasColumn('assets', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if (! empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
