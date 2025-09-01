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
        Schema::create('signature_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('signature_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->string('action'); // create, confirm, view-url, update, delete
            $table->string('ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('event_id')->nullable(); // a short GUID for correlating logs
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signature_audits');
    }
};
