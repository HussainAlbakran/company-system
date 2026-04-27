<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('architect_material_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('architect_material_requests', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('notes');
            }

            if (! Schema::hasColumn('architect_material_requests', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('submitted_at');
            }

            if (! Schema::hasColumn('architect_material_requests', 'approved_by')) {
                $table->foreignId('approved_by')
                    ->nullable()
                    ->after('approved_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        // Align legacy "received" with new status model.
        DB::table('architect_material_requests')
            ->where('status', 'received')
            ->update(['status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('architect_material_requests', function (Blueprint $table) {
            if (Schema::hasColumn('architect_material_requests', 'approved_by')) {
                $table->dropConstrainedForeignId('approved_by');
            }

            if (Schema::hasColumn('architect_material_requests', 'approved_at')) {
                $table->dropColumn('approved_at');
            }

            if (Schema::hasColumn('architect_material_requests', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });
    }
};
