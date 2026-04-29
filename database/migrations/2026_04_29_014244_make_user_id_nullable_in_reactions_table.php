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
        Schema::table('reactions', function (Blueprint $blueprint) {
            $blueprint->index('relationship_id'); // Ensure FK index exists
            $blueprint->dropUnique('reactions_relationship_id_post_id_user_id_type_unique');
            $blueprint->unsignedBigInteger('user_id')->nullable()->change();
            $blueprint->string('guest_id')->nullable()->after('user_id');
            $blueprint->unique(['relationship_id', 'post_id', 'user_id', 'guest_id', 'type'], 'reactions_full_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reactions', function (Blueprint $blueprint) {
            $blueprint->dropUnique('reactions_unique');
            $blueprint->foreignId('user_id')->nullable(false)->change();
            $blueprint->dropColumn('guest_id');
            $blueprint->unique(['relationship_id', 'post_id', 'user_id', 'type']);
        });
    }
};
