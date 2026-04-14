<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Aligns `purchases` with application usage:
     * - `project_id` nullable: general purchases (asset_purchase / general_maintenance) have no project.
     * - `type` as string: supports contract_purchase, asset_purchase, general_maintenance, and legacy purchase/repair.
     */
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable()->change();
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->nullOnDelete();
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->string('type', 50)->default('purchase')->change();
        });
    }

    /**
     * Reverts column shapes; may fail if rows exist with types outside purchase/repair or null project_id.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable(false)->change();
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->cascadeOnDelete();
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->enum('type', ['purchase', 'repair'])->default('purchase')->change();
        });
    }
};
