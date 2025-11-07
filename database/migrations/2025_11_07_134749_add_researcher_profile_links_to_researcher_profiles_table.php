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
        Schema::table('researcher_profiles', function (Blueprint $table) {
            $table->string('scopus_link')->nullable()->after('profile_link');
            $table->string('orcid_link')->nullable()->after('scopus_link');
            $table->string('wos_link')->nullable()->after('orcid_link');
            $table->string('google_scholar_link')->nullable()->after('wos_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('researcher_profiles', function (Blueprint $table) {
            $table->dropColumn(['scopus_link', 'orcid_link', 'wos_link', 'google_scholar_link']);
        });
    }
};
