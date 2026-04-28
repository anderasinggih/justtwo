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
        Schema::create('public_settings', function (Blueprint $table) {
            $table->id();
            $table->string('theme')->default('light');
            $table->string('hero_title')->default('capturing the moments that define our journey.');
            $table->text('hero_subtitle')->nullable();
            $table->json('banner_paths')->nullable();
            $table->timestamps();
        });

        // Insert default row
        \App\Models\PublicSetting::create([
            'hero_title' => 'capturing the moments that define our journey.',
            'hero_subtitle' => 'a small window into our private gallery. selected memories shared with the world, preserved forever in our little corner of the internet.',
            'banner_paths' => [],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_settings');
    }
};
