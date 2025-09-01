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
        Schema::table('signatures', function (Blueprint $table) {
            // Add only the missing columns that we need
            $table->string('label', 120)->nullable()->after('user_id');
            $table->string('object_key')->nullable()->after('label');
            $table->unsignedInteger('width_px')->nullable()->after('mime_type');
            $table->unsignedInteger('height_px')->nullable()->after('width_px');
            $table->string('hash_sha256', 64)->nullable()->after('height_px');
            $table->binary('encrypted_meta')->nullable()->after('hash_sha256');
            $table->softDeletes()->after('updated_at');
        });

        // Update existing records with default values for required fields
        DB::table('signatures')->update([
            'object_key' => DB::raw("'signatures/default/' || id || '.png'"),
        ]);

        // Now make required columns NOT NULL and add constraints
        Schema::table('signatures', function (Blueprint $table) {
            $table->string('object_key')->nullable(false)->change();
            
            // Add unique constraint and indexes
            $table->unique('object_key');
            $table->index('hash_sha256');
            $table->index(['user_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signatures', function (Blueprint $table) {
            // Drop indexes and constraints
            $table->dropIndex(['user_id', 'deleted_at']);
            $table->dropIndex(['hash_sha256']);
            $table->dropUnique(['object_key']);
            
            // Drop all added columns
            $table->dropSoftDeletes();
            $table->dropColumn([
                'label',
                'object_key',
                'width_px',
                'height_px',
                'hash_sha256',
                'encrypted_meta',
            ]);
        });
    }
};
