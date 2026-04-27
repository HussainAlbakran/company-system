<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (! Schema::hasColumn('purchases', 'architect_material_request_id')) {
                $table->foreignId('architect_material_request_id')
                    ->nullable()
                    ->after('project_id')
                    ->constrained('architect_material_requests')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'architect_material_request_id')) {
                $table->dropConstrainedForeignId('architect_material_request_id');
            }
        });
    }
};
