<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('factories', function (Blueprint $table) {
            if (! Schema::hasColumn('factories', 'manager_user_id')) {
                $table->foreignId('manager_user_id')
                    ->nullable()
                    ->after('receiver_name')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('factories', function (Blueprint $table) {
            if (Schema::hasColumn('factories', 'manager_user_id')) {
                $table->dropConstrainedForeignId('manager_user_id');
            }
        });
    }
};
