<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'role')) {
            return;
        }

        DB::table('users')
            ->where('role', 'client')
            ->update([
                'role' => 'sales',
                'is_active' => false,
                'approval_status' => 'suspended',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Intentional no-op. We do not restore removed client role automatically.
    }
};
