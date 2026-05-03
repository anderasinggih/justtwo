<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('relationship_id')->constrained()->cascadeOnDelete();
            $blueprint->string('title');
            $blueprint->decimal('target_amount', 15, 2);
            $blueprint->decimal('current_amount', 15, 2)->default(0);
            $blueprint->string('icon')->nullable();
            $blueprint->timestamps();
        });

        Schema::create('saving_logs', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('saving_id')->constrained()->cascadeOnDelete();
            $blueprint->foreignId('user_id')->constrained()->cascadeOnDelete();
            $blueprint->decimal('amount', 15, 2);
            $blueprint->string('note')->nullable();
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saving_logs');
        Schema::dropIfExists('savings');
    }
};
