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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->boolean('bluetick')->default(false);
            $table->string('email')->unique();
            $table->string('profile')->nullable()->default('https://res.cloudinary.com/ddrivhxfq/image/upload/v1687330227/Screenshot_91_g4j2oa.png');
            $table->string('cover')->nullable()->default('https://res.cloudinary.com/ddrivhxfq/image/upload/v1687330452/the-magic-islands-of-lofoten-norway-europe-winter-morning-light-landscape-desktop-hd-wallpaper-for-pc-tablet-and-mobile-3840_2160-wallpaper-preview_phrarz.jpg');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
