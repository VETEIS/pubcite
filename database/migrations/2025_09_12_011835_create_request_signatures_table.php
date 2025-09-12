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
        Schema::create('request_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('signatory_role', 100); // e.g., 'Faculty', 'Research Center Manager', 'College Dean'
            $table->string('signatory_name', 255); // The actual name from the form
            $table->timestamp('signed_at');
            $table->string('signed_document_path')->nullable(); // Path to the signed document
            $table->string('original_document_path')->nullable(); // Path to the original document
            $table->timestamps();
            
            // Ensure one signature per signatory per request
            $table->unique(['request_id', 'user_id']);
            
            // Indexes for performance
            $table->index(['request_id', 'signed_at']);
            $table->index(['user_id', 'signed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_signatures');
    }
};