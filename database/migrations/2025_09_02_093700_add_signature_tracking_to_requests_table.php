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
            // Signature tracking fields
            $table->enum('signature_status', ['pending', 'signed'])->default('pending')->after('status');
            $table->timestamp('signed_at')->nullable()->after('signature_status');
            $table->unsignedBigInteger('signed_by')->nullable()->after('signed_at');
            $table->string('signed_document_path')->nullable()->after('signed_by');
            $table->string('original_document_path')->nullable()->after('signed_document_path');
            
            // Foreign key for signed_by
            $table->foreign('signed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropForeign(['signed_by']);
            $table->dropColumn([
                'signature_status',
                'signed_at',
                'signed_by',
                'signed_document_path',
                'original_document_path'
            ]);
        });
    }
};
