<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sales_contracts', function (Blueprint $table) {
            $table->string('payment_type')->nullable();
            $table->decimal('full_payment_amount', 12, 2)->nullable();

            $table->string('first_payment_title')->nullable();
            $table->decimal('first_payment_percentage', 5, 2)->nullable();
            $table->decimal('first_payment_amount', 12, 2)->nullable();
            $table->date('first_payment_due_date')->nullable();
        });
    }

    public function down()
    {
        Schema::table('sales_contracts', function (Blueprint $table) {
            $table->dropColumn([
                'payment_type',
                'full_payment_amount',
                'first_payment_title',
                'first_payment_percentage',
                'first_payment_amount',
                'first_payment_due_date',
            ]);
        });
    }
};