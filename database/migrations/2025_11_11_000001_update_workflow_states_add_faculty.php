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
        // Add a new enum column that includes pending_faculty and all other workflow states
        Schema::table('requests', function (Blueprint $table) {
            $table->enum('workflow_state_new', [
                'pending_research_manager',
                'pending_faculty',
                'pending_dean',
                'pending_deputy_director',
                'pending_director',
                'completed'
            ])->default('pending_research_manager')->after('status');
        });

        // Copy values from old workflow_state into the new column (where present)
        DB::statement("UPDATE requests SET workflow_state_new = workflow_state");

        // Drop index on old column if it exists
        try {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropIndex(['workflow_state']);
            });
        } catch (\Throwable $e) {
            // index might not exist; ignore
        }

        // Drop old column and rename the new one to workflow_state
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn('workflow_state');
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->renameColumn('workflow_state_new', 'workflow_state');
            $table->index('workflow_state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the previous set without pending_faculty
        Schema::table('requests', function (Blueprint $table) {
            $table->enum('workflow_state_old', [
                'pending_research_manager',
                'pending_dean',
                'pending_deputy_director',
                'pending_director',
                'completed'
            ])->default('pending_research_manager')->after('status');
        });

        DB::statement("UPDATE requests SET workflow_state_old = workflow_state");

        try {
            Schema::table('requests', function (Blueprint $table) {
                $table->dropIndex(['workflow_state']);
            });
        } catch (\Throwable $e) {
        }

        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn('workflow_state');
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->renameColumn('workflow_state_old', 'workflow_state');
            $table->index('workflow_state');
        });
    }
};


