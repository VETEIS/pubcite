<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            // $table->json('form_data')->nullable()->after('status'); // Already exists, do not add again
            // $table->string('pdf_path')->nullable()->after('form_data'); // Already exists, do not add again
            // $table->text('pdf_content')->nullable()->after('pdf_path'); // Already exists, do not add again
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['form_data', 'pdf_path', 'pdf_content']);
        });
    }
};
