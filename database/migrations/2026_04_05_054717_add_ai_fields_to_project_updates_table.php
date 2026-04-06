<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_updates', function (Blueprint $table) {

            //  النص المستخرج من المرفق
            $table->longText('extracted_text')->nullable()->after('attachment');

            // \ الأرقام المستخرجة
            $table->json('extracted_numbers_json')->nullable()->after('extracted_text');

            // \ ملخص AI
            $table->longText('ai_summary')->nullable()->after('extracted_numbers_json');

            // \ حالة المعالجة
            $table->string('processing_status')->default('pending')->after('ai_summary');
        });
    }

    public function down(): void
    {
        Schema::table('project_updates', function (Blueprint $table) {
            $table->dropColumn([
                'extracted_text',
                'extracted_numbers_json',
                'ai_summary',
                'processing_status',
            ]);
        });
    }
};