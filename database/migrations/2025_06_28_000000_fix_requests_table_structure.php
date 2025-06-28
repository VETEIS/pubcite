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
        // Drop the existing table if it exists (this will be a fresh start)
        Schema::dropIfExists('requests');
        
        // Create the requests table with the correct structure
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('request_code');
            $table->string('type');
            $table->string('status')->default('pending'); // No enum constraint
            $table->dateTime('requested_at');
            $table->json('form_data')->nullable();
            $table->string('pdf_path')->nullable();
            $table->longText('pdf_content')->nullable();
            $table->string('token')->nullable()->unique();
            
            // Add indexes for better performance
            $table->index(['user_id']);
            $table->index(['request_code']);
            $table->index(['status']);
            $table->index(['requested_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
}; 