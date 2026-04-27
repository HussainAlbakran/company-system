<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * restrictOnDelete: deleting a user who is still assigned as portal client on any
         * project fails at the database level — no orphaned FK and no silent nulling.
         * To remove a client user, first clear or reassign client_user_id on their projects.
         */
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('client_user_id')
                ->nullable()
                ->after('updated_by')
                ->constrained('users')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_user_id');
        });
    }
};
