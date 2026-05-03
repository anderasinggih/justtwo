<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('relationship_id')->constrained()->cascadeOnDelete();
            $blueprint->string('title');
            $blueprint->date('target_date')->nullable();
            $blueprint->decimal('total_budget', 15, 2)->default(0);
            $blueprint->string('cover_image')->nullable();
            $blueprint->text('description')->nullable();
            $blueprint->enum('status', ['planning', 'ongoing', 'completed'])->default('planning');
            $blueprint->timestamps();
        });

        Schema::create('plan_expenses', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $blueprint->string('title');
            $blueprint->decimal('amount', 15, 2);
            $blueprint->string('category')->default('general'); // food, transport, lodging, etc.
            $blueprint->boolean('is_paid')->default(false);
            $blueprint->timestamps();
        });

        Schema::create('plan_itineraries', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $blueprint->date('event_date');
            $blueprint->time('event_time')->nullable();
            $blueprint->string('activity');
            $blueprint->text('notes')->nullable();
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_itineraries');
        Schema::dropIfExists('plan_expenses');
        Schema::dropIfExists('plans');
    }
};
