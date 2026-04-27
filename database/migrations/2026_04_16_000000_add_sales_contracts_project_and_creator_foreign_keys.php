<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sales_contracts')) {
            return;
        }

        // Clean orphan references before adding constraints.
        DB::table('sales_contracts')
            ->whereNotNull('project_id')
            ->whereNotIn('project_id', DB::table('projects')->select('id'))
            ->update(['project_id' => null]);

        DB::table('sales_contracts')
            ->whereNotNull('created_by')
            ->whereNotIn('created_by', DB::table('users')->select('id'))
            ->update(['created_by' => null]);

        Schema::table('sales_contracts', function (Blueprint $table) {
            $table->index('project_id', 'sales_contracts_project_id_idx');
            $table->index('created_by', 'sales_contracts_created_by_idx');

            $table->foreign('project_id', 'sales_contracts_project_id_fk')
                ->references('id')
                ->on('projects')
                ->nullOnDelete();

            $table->foreign('created_by', 'sales_contracts_created_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('sales_contracts')) {
            return;
        }

        Schema::table('sales_contracts', function (Blueprint $table) {
            $table->dropForeign('sales_contracts_project_id_fk');
            $table->dropForeign('sales_contracts_created_by_fk');

            $table->dropIndex('sales_contracts_project_id_idx');
            $table->dropIndex('sales_contracts_created_by_idx');
        });
    }
};
