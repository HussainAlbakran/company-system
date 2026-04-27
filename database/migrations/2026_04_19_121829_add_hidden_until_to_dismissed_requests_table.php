<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dismissed_requests', function (Blueprint $table) {
            $table->timestamp('hidden_until')->nullable()->after('dismissed_at');
        });

        // Legacy rows: expire immediately so they do not stay hidden under hidden_until logic
        DB::table('dismissed_requests')->update([
            'hidden_until' => now()->subSecond(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dismissed_requests', function (Blueprint $table) {
            $table->dropColumn('hidden_until');
        });
    }
};
