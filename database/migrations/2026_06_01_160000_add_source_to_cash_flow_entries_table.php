<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_flow_entries', function (Blueprint $table) {
            $table->string('source_type')->default('manual')->after('notes');
            $table->unsignedBigInteger('source_id')->nullable()->after('source_type');

            $table->unique(['source_type', 'source_id'], 'cash_flow_entries_source_unique');
        });
    }

    public function down(): void
    {
        Schema::table('cash_flow_entries', function (Blueprint $table) {
            $table->dropUnique('cash_flow_entries_source_unique');
            $table->dropColumn(['source_type', 'source_id']);
        });
    }
};
