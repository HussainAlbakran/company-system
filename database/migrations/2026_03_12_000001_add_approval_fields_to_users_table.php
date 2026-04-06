<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('approval_status')->default('pending')->after('role');
            $table->boolean('is_active')->default(false)->after('approval_status');
            $table->timestamp('approved_at')->nullable()->after('is_active');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'approval_status',
                'is_active',
                'approved_at',
                'approved_by',
            ]);
        });
    }
};