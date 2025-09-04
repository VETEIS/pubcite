<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('request_code');
            $table->string('type');
            $table->enum('status', ['pending', 'endorsed', 'rejected'])->default('pending');
            $table->dateTime('requested_at');
            $table->string('token')->nullable()->unique();
            $table->json('form_data')->nullable();
            $table->json('pdf_path')->nullable();
            $table->longText('pdf_content')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('requests');
    }
}; 