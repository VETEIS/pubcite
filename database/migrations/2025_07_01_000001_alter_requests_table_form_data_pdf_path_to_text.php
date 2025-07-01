<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->text('form_data')->change();
            $table->text('pdf_path')->change();
        });
    }

    public function down()
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->string('form_data', 255)->change();
            $table->string('pdf_path', 255)->change();
        });
    }
}; 