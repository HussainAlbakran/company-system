<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('department_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->foreignId('responsible_employee_id')
                  ->nullable()
                  ->constrained('employees')
                  ->nullOnDelete();

            $table->string('name');

            $table->text('description')->nullable();

            $table->date('start_date');

            $table->date('end_date');

            $table->unsignedTinyInteger('progress_percentage')->default(0);

            $table->decimal('project_value', 15, 2)->default(0);

            $table->decimal('expenses', 15, 2)->default(0);

            $table->string('status')->default('ongoing');

            $table->string('project_pdf')->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};