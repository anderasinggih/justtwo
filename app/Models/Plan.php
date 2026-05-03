<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'relationship_id',
        'saving_id',
        'title',
        'target_date',
        'total_budget',
        'cover_image',
        'description',
        'status',
        'priority',
        'category',
    ];

    public function savingGoal(): BelongsTo
    {
        return $this->belongsTo(Saving::class, 'saving_id');
    }

    protected $casts = [
        'target_date' => 'date',
    ];

    public function relationship(): BelongsTo
    {
        return $this->belongsTo(Relationship::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(PlanExpense::class);
    }

    public function itineraries(): HasMany
    {
        return $this->hasMany(PlanItinerary::class)->orderBy('event_date')->orderBy('event_time');
    }

    public function getSpentBudgetAttribute(): float
    {
        return $this->expenses()->sum('amount');
    }

    public function getBudgetProgressAttribute(): float
    {
        if ($this->total_budget <= 0) return 0;
        return min(100, ($this->getSpentBudgetAttribute() / $this->total_budget) * 100);
    }

    public function getIconAttribute(): string
    {
        $title = strtolower($this->title);
        
        if (str_contains($title, 'nikah') || str_contains($title, 'wedding') || str_contains($title, 'kawin')) return '💍';
        if (str_contains($title, 'rumah') || str_contains($title, 'home') || str_contains($title, 'apart')) return '🏠';
        if (str_contains($title, 'makan') || str_contains($title, 'dinner') || str_contains($title, 'date') || str_contains($title, 'resto')) return '🍽️';
        if (str_contains($title, 'nonton') || str_contains($title, 'movie') || str_contains($title, 'cinema') || str_contains($title, 'film')) return '🎬';
        if (str_contains($title, 'jalan') || str_contains($title, 'trip') || str_contains($title, 'libur') || str_contains($title, 'bali') || str_contains($title, 'travel')) return '✈️';
        if (str_contains($title, 'beli') || str_contains($title, 'shop')) return '🛍️';
        if (str_contains($title, 'pesta') || str_contains($title, 'party') || str_contains($title, 'ultah') || str_contains($title, 'birth')) return '🎉';
        
        return '✨';
    }
}
