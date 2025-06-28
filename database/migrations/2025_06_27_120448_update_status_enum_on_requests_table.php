<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // For SQLite: emulate enum change by recreating the column
        Schema::table('requests', function (Blueprint $table) {
            $table->string('status_tmp')->default('pending');
        });
        // Copy values
        DB::statement("UPDATE requests SET status_tmp = status");
        // Drop old column
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        // Rename new column
        Schema::table('requests', function (Blueprint $table) {
            $table->renameColumn('status_tmp', 'status');
        });
    }

    public function down(): void
    {
        // Revert to original enum (pending, endorsed, rejected)
        Schema::table('requests', function (Blueprint $table) {
            $table->string('status_tmp')->default('pending');
        });
        DB::statement("UPDATE requests SET status_tmp = status");
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('requests', function (Blueprint $table) {
            $table->renameColumn('status_tmp', 'status');
        });
    }
}; 