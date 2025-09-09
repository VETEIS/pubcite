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
        // Drop the existing check constraint and recreate the column as string
        \DB::statement('ALTER TABLE requests DROP CONSTRAINT IF EXISTS requests_status_check;');
        \DB::statement('ALTER TABLE requests ALTER COLUMN status TYPE VARCHAR(255);');
        \DB::statement('ALTER TABLE requests ALTER COLUMN status SET DEFAULT \'pending\';');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the enum constraint (if needed)
        \DB::statement('ALTER TABLE requests ALTER COLUMN status TYPE VARCHAR(255);');
        \DB::statement('ALTER TABLE requests ADD CONSTRAINT requests_status_check CHECK (status IN (\'pending\', \'endorsed\', \'rejected\'));');
    }
};
