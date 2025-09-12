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
        // Convert existing signatory names to uppercase
        DB::table('users')
            ->where('role', 'signatory')
            ->update([
                'name' => DB::raw('UPPER(name)')
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This migration cannot be easily reversed as we don't know
        // the original case of the names. This is a one-way migration.
        // If rollback is needed, it would require manual intervention.
    }
};
