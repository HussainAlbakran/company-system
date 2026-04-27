<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('assets')) {
            return;
        }

        Schema::table('assets', function (Blueprint $table) {
            if (! Schema::hasColumn('assets', 'asset_type')) {
                $table->string('asset_type')->default('general')->after('name');
            }
            if (! Schema::hasColumn('assets', 'plate_number')) {
                $table->string('plate_number')->nullable()->after('serial_number');
            }
            if (! Schema::hasColumn('assets', 'color')) {
                $table->string('color')->nullable()->after('plate_number');
            }
            if (! Schema::hasColumn('assets', 'inspection_expiry_date')) {
                $table->date('inspection_expiry_date')->nullable()->after('color');
            }
        });

        DB::table('assets')
            ->whereNull('serial_number')
            ->orWhere('serial_number', '')
            ->orderBy('id')
            ->get(['id'])
            ->each(function ($asset): void {
                DB::table('assets')
                    ->where('id', $asset->id)
                    ->update(['serial_number' => 'AST-MIG-' . str_pad((string) $asset->id, 6, '0', STR_PAD_LEFT)]);
            });

        Schema::table('assets', function (Blueprint $table) {
            $table->unique('serial_number', 'assets_serial_number_unique');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('assets')) {
            return;
        }

        Schema::table('assets', function (Blueprint $table) {
            if (Schema::hasColumn('assets', 'serial_number')) {
                $table->dropUnique('assets_serial_number_unique');
            }

            $dropColumns = [];
            foreach (['asset_type', 'plate_number', 'color', 'inspection_expiry_date'] as $column) {
                if (Schema::hasColumn('assets', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if (! empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
