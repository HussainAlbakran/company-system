<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_maintenance_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('asset_maintenance_logs', 'ended_at')) {
                $table->timestamp('ended_at')->nullable()->after('maintenance_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('asset_maintenance_logs', function (Blueprint $table) {
            if (Schema::hasColumn('asset_maintenance_logs', 'ended_at')) {
                $table->dropColumn('ended_at');
            }
        });
    }
};
