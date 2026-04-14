<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('project_code')->nullable()->unique()->after('id');
            $table->unsignedBigInteger('sales_contract_id')->nullable()->after('project_code');
            $table->string('client_name')->nullable()->after('name');
            $table->string('main_contractor')->nullable()->after('client_name');

            $table->enum('current_stage', [
                'sales',
                'architect',
                'purchasing',
                'production_installation',
                'completed',
            ])->default('sales')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'project_code',
                'sales_contract_id',
                'client_name',
                'main_contractor',
                'current_stage',
            ]);
        });
    }
};