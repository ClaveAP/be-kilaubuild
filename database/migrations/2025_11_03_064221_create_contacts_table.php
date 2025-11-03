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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('no_telp');
            $table->string('alamat');
            $table->string('link_gmaps');
            $table->string('email');
            $table->string('url_instagram');
            $table->string('url_facebook');
            $table->string('url_threads');
            $table->string('url_tiktok');
            $table->string('url_youtube');
            $table->string('url_twitter');
            $table->string('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
