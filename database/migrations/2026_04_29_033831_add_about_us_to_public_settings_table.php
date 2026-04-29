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
            $table->text('about_us')->nullable()->after('hero_subtitle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('public_settings', function (Blueprint $table) {
            $table->dropColumn('about_us');
        });
    }
};
