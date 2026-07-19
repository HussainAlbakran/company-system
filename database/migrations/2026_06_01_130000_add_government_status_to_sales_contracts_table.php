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

        if (! Schema::hasColumn('sales_contracts', 'status')) {
            return;
        }

        DB::statement("
            UPDATE sales_contracts
            SET status = 'awaiting_payment'
            WHERE status IS NULL
               OR status NOT IN (
                   'draft','approved','rejected',
                   'awaiting_payment','partial','paid','pending','government'
               )
        ");

        DB::statement("
            ALTER TABLE sales_contracts
            MODIFY status ENUM(
                'draft','approved','rejected',
                'awaiting_payment','partial','paid','pending','government'
            ) NOT NULL DEFAULT 'draft'
        ");
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        if (! Schema::hasColumn('sales_contracts', 'status')) {
            return;
        }

        DB::statement("
            UPDATE sales_contracts
            SET status = 'awaiting_payment'
            WHERE status = 'government'
        ");

        DB::statement("
            ALTER TABLE sales_contracts
            MODIFY status ENUM(
                'draft','approved','rejected',
                'awaiting_payment','partial','paid','pending'
            ) NOT NULL DEFAULT 'draft'
        ");
    }
};
