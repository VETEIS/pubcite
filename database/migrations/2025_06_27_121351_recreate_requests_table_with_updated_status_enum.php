<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create new table with correct status constraint
        Schema::create('requests_new', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('request_code');
            $table->string('type');
            $table->string('status'); // No enum, but we will enforce in app
            $table->dateTime('requested_at');
            $table->json('form_data')->nullable();
            $table->string('pdf_path')->nullable();
            $table->longText('pdf_content')->nullable();
            $table->string('token')->nullable()->unique();
        });
        // 2. Copy data
        $columns = [
            'id', 'user_id', 'request_code', 'type', 'status', 'requested_at', 'form_data', 'pdf_path', 'pdf_content', 'token'
        ];
        $columnsList = implode(',', $columns);
        DB::statement("INSERT INTO requests_new ($columnsList) SELECT $columnsList FROM requests");
        // 3. Drop old table
        Schema::drop('requests');
        // 4. Rename new table
        Schema::rename('requests_new', 'requests');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not implemented: would require recreating the old enum/check constraint
    }
};
