<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        if (Schema::hasColumn('sales_contracts', 'status')) {
            // Normalize any legacy/unknown statuses first so enum alteration cannot fail.
            DB::statement("
                UPDATE sales_contracts
                SET status = 'awaiting_payment'
                WHERE status IS NULL
                   OR status NOT IN ('draft','approved','rejected','awaiting_payment','partial','paid','pending')
            ");

            DB::statement("
                ALTER TABLE sales_contracts
                MODIFY status ENUM('draft','approved','rejected','awaiting_payment','partial','paid','pending')
                NOT NULL DEFAULT 'draft'
            ");
        }

        if (Schema::hasColumn('projects', 'current_stage')) {
            // Keep data safe if old databases have stage values outside the enum list.
            DB::statement("
                UPDATE projects
                SET current_stage = 'production_installation'
                WHERE current_stage IS NOT NULL
                  AND current_stage NOT IN ('sales','contracts','architect','purchasing','production_installation','completed')
            ");

            DB::statement("
                ALTER TABLE projects
                MODIFY current_stage ENUM('sales','contracts','architect','purchasing','production_installation','completed')
                NOT NULL DEFAULT 'sales'
            ");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        if (Schema::hasColumn('sales_contracts', 'status')) {
            DB::statement("
                UPDATE sales_contracts
                SET status = 'draft'
                WHERE status IN ('awaiting_payment','partial','paid','pending')
            ");

            DB::statement("
                ALTER TABLE sales_contracts
                MODIFY status ENUM('draft','approved','rejected')
                NOT NULL DEFAULT 'draft'
            ");
        }

        if (Schema::hasColumn('projects', 'current_stage')) {
            DB::statement("
                UPDATE projects
                SET current_stage = 'sales'
                WHERE current_stage = 'contracts'
            ");

            DB::statement("
                ALTER TABLE projects
                MODIFY current_stage ENUM('sales','architect','purchasing','production_installation','completed')
                NOT NULL DEFAULT 'sales'
            ");
        }
    }
};

