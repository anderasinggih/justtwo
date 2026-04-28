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
        Schema::table('post_media', function (Blueprint $table) {
            $table->renameColumn('file_path', 'file_path_original');
            $table->renameColumn('order', 'sort_order');
        });

        Schema::table('post_media', function (Blueprint $table) {
            $table->string('original_file_name')->after('post_id')->nullable();
            $table->string('file_path_thumbnail')->after('file_path_original')->nullable();
            $table->unsignedInteger('file_size_kb')->after('file_type')->nullable();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_media', function (Blueprint $table) {
            $table->dropColumn(['original_file_name', 'file_path_thumbnail', 'file_size_kb']);
            $table->renameColumn('file_path_original', 'file_path');
            $table->renameColumn('sort_order', 'order');
        });
    }

};
