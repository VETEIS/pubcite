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
            $table->enum('workflow_state', [
                'pending_research_manager',
                'pending_dean', 
                'completed'
            ])->default('pending_research_manager')->after('status');
            
            $table->index('workflow_state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropIndex(['workflow_state']);
            $table->dropColumn('workflow_state');
        });
    }
};
