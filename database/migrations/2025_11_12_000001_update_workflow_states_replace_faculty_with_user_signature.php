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
        // Add a new enum column that replaces pending_faculty with pending_user_signature
        Schema::table('requests', function (Blueprint $table) {
            $table->enum('workflow_state_new', [
                'pending_user_signature',
                'pending_research_manager',
                'pending_dean',
                'pending_deputy_director',
                'pending_director',
                'completed'
            ])->default('pending_user_signature')->after('status');
        });

        // Copy values from old workflow_state into the new column
        // Convert pending_faculty to pending_user_signature
        // Convert pending_research_manager to pending_user_signature if it's the first state
        DB::statement("
            UPDATE requests 
            SET workflow_state_new = CASE 
                WHEN workflow_state = 'pending_faculty' THEN 'pending_user_signature'
                WHEN workflow_state = 'pending_research_manager' AND NOT EXISTS (
                    SELECT 1 FROM request_signatures WHERE request_signatures.request_id = requests.id
                ) THEN 'pending_user_signature'
                ELSE workflow_state
            END
        ");

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
        // Revert to the previous set with pending_faculty
        Schema::table('requests', function (Blueprint $table) {
            $table->enum('workflow_state_old', [
                'pending_research_manager',
                'pending_faculty',
                'pending_dean',
                'pending_deputy_director',
                'pending_director',
                'completed'
            ])->default('pending_research_manager')->after('status');
        });

        // Convert pending_user_signature back to pending_faculty
        DB::statement("
            UPDATE requests 
            SET workflow_state_old = CASE 
                WHEN workflow_state = 'pending_user_signature' THEN 'pending_faculty'
                ELSE workflow_state
            END
        ");

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

