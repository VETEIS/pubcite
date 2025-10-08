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
        Schema::create('researcher_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->json('research_areas'); // Array of research areas/tags
            $table->text('bio');
            $table->string('status_badge'); // Active, Research, Innovation, Leadership, etc.
            $table->string('photo_path')->nullable(); // Profile photo file path
            $table->string('background_color')->default('maroon'); // Background color for card
            $table->string('profile_link')->nullable(); // Link for "View Profile" button
            $table->integer('sort_order')->default(0); // For ordering researchers
            $table->boolean('is_active')->default(true); // Show/hide researcher
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('researcher_profiles');
    }
};
