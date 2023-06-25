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
        Schema::create('hashtag_tweet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hashtag_id')->constrained('hashtag')->onDelete('cascade');
            $table->foreignId('tweet_id')->constrained('tweets')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hashtag_tweet');
    }
};
