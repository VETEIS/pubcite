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
        // Remove the enum constraint on status column to allow 'draft' status
        Schema::table('requests', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the enum constraint (if needed)
        Schema::table('requests', function (Blueprint $table) {
            $table->enum('status', ['pending', 'endorsed', 'rejected'])->default('pending')->change();
        });
    }
};
