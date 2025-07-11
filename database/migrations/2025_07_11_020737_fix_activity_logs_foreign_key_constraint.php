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
        Schema::table('activity_logs', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['request_id']);
            
            // Add the foreign key constraint with SET NULL instead of CASCADE
            // This way, when a request is deleted, the activity log entry remains
            // but the request_id becomes NULL
            $table->foreign('request_id')->references('id')->on('requests')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            // Drop the SET NULL foreign key constraint
            $table->dropForeign(['request_id']);
            
            // Restore the original CASCADE foreign key constraint
            $table->foreign('request_id')->references('id')->on('requests')->onDelete('cascade');
        });
    }
};
