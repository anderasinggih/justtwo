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
        Schema::table('public_settings', function (Blueprint $table) {
            $table->string('journey_video_url_2')->nullable()->after('journey_video_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('public_settings', function (Blueprint $table) {
            $table->dropColumn('journey_video_url_2');
        });
    }
};
