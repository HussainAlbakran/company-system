<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('architect_tasks', function (Blueprint $table) {

            $table->string('drawing_type')->nullable();
            $table->string('drawing_file')->nullable();
            $table->string('planning_file')->nullable();
            $table->text('notes')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('architect_tasks', function (Blueprint $table) {

            $table->dropColumn([
                'drawing_type',
                'drawing_file',
                'planning_file',
                'notes'
            ]);

        });
    }
};