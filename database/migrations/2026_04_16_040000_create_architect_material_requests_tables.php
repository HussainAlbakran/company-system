<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('architect_material_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('draft');
            $table->text('notes')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->index(['created_by', 'status']);
        });

        Schema::create('architect_material_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('architect_material_requests')->cascadeOnDelete();
            $table->string('material_name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 12, 2);
            $table->string('unit', 50);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('architect_material_request_items');
        Schema::dropIfExists('architect_material_requests');
    }
};
