<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->timestamp('approved_at')->nullable()->after('status');
            $table->boolean('is_deducted')->default(false)->after('approved_at');
            $table->timestamp('deducted_at')->nullable()->after('is_deducted');
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn([
                'approved_at',
                'is_deducted',
                'deducted_at',
            ]);
        });
    }
};