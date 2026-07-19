<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cash_flow_entries')) {
            return;
        }

        DB::statement("ALTER TABLE cash_flow_entries MODIFY COLUMN type VARCHAR(20) NOT NULL");
    }

    public function down(): void
    {
        if (! Schema::hasTable('cash_flow_entries')) {
            return;
        }

        DB::statement("ALTER TABLE cash_flow_entries MODIFY COLUMN type ENUM('income', 'expense') NOT NULL");
    }
};
