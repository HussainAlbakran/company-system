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
        // Safely null orphan values before adding constraints.
        DB::table('employees')
            ->whereNotNull('department_id')
            ->whereNotIn('department_id', DB::table('departments')->select('id'))
            ->update(['department_id' => null]);

        DB::table('employees')
            ->whereNotNull('factory_id')
            ->whereNotIn('factory_id', DB::table('factories')->select('id'))
            ->update(['factory_id' => null]);

        DB::table('employees')
            ->whereNotNull('user_id')
            ->whereNotIn('user_id', DB::table('users')->select('id'))
            ->update(['user_id' => null]);

        DB::table('production_entries')
            ->whereNotNull('employee_id')
            ->whereNotIn('employee_id', DB::table('employees')->select('id'))
            ->update(['employee_id' => null]);

        DB::table('users')
            ->whereNotNull('approved_by')
            ->whereNotIn('approved_by', DB::table('users')->select('id'))
            ->update(['approved_by' => null]);

        DB::table('audit_logs')
            ->whereNotNull('user_id')
            ->whereNotIn('user_id', DB::table('users')->select('id'))
            ->update(['user_id' => null]);

        Schema::table('employees', function (Blueprint $table) {
            $table->index('department_id', 'employees_department_id_idx');
            $table->index('factory_id', 'employees_factory_id_idx');
            $table->index('user_id', 'employees_user_id_idx');

            $table->foreign('department_id', 'employees_department_id_fk')
                ->references('id')
                ->on('departments')
                ->nullOnDelete();

            $table->foreign('factory_id', 'employees_factory_id_fk')
                ->references('id')
                ->on('factories')
                ->nullOnDelete();

            $table->foreign('user_id', 'employees_user_id_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });

        Schema::table('production_entries', function (Blueprint $table) {
            $table->index('employee_id', 'production_entries_employee_id_idx');

            $table->foreign('employee_id', 'production_entries_employee_id_fk')
                ->references('id')
                ->on('employees')
                ->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('approved_by', 'users_approved_by_idx');

            $table->foreign('approved_by', 'users_approved_by_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index('user_id', 'audit_logs_user_id_idx');
            $table->index(['model', 'model_id'], 'audit_logs_model_model_id_idx');

            $table->foreign('user_id', 'audit_logs_user_id_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign('audit_logs_user_id_fk');
            $table->dropIndex('audit_logs_user_id_idx');
            $table->dropIndex('audit_logs_model_model_id_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_approved_by_fk');
            $table->dropIndex('users_approved_by_idx');
        });

        Schema::table('production_entries', function (Blueprint $table) {
            $table->dropForeign('production_entries_employee_id_fk');
            $table->dropIndex('production_entries_employee_id_idx');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign('employees_department_id_fk');
            $table->dropForeign('employees_factory_id_fk');
            $table->dropForeign('employees_user_id_fk');

            $table->dropIndex('employees_department_id_idx');
            $table->dropIndex('employees_factory_id_idx');
            $table->dropIndex('employees_user_id_idx');
        });
    }
};
