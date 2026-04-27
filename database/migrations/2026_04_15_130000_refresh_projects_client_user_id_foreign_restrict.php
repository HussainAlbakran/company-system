<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * For databases that already ran the earlier client_user_id migration with nullOnDelete(),
 * this replaces the foreign key with restrictOnDelete(). Fresh installs only need
 * 2026_04_15_120000 (which already uses restrictOnDelete); this migration is safe to run
 * after it and will re-create the same constraint.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('projects', 'client_user_id')) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['client_user_id']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('client_user_id')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('projects', 'client_user_id')) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['client_user_id']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('client_user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }
};
