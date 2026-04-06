<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_updates', function (Blueprint $table) {
            if (! Schema::hasColumn('project_updates', 'attachment')) {
                $table->string('attachment')->nullable()->after('progress');
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_updates', function (Blueprint $table) {
            if (Schema::hasColumn('project_updates', 'attachment')) {
                $table->dropColumn('attachment');
            }
        });
    }
};