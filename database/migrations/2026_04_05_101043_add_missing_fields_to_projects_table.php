<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'project_pdf')) {
                $table->string('project_pdf')->nullable()->after('status');
            }

            if (!Schema::hasColumn('projects', 'notes')) {
                $table->text('notes')->nullable()->after('project_pdf');
            }

            if (!Schema::hasColumn('projects', 'created_by')) {
                $table->foreignId('created_by')
                    ->nullable()
                    ->after('notes')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('projects', 'updated_by')) {
                $table->foreignId('updated_by')
                    ->nullable()
                    ->after('created_by')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'updated_by')) {
                $table->dropConstrainedForeignId('updated_by');
            }

            if (Schema::hasColumn('projects', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }

            if (Schema::hasColumn('projects', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('projects', 'project_pdf')) {
                $table->dropColumn('project_pdf');
            }
        });
    }
};