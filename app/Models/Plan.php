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
}
