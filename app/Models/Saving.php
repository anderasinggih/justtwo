<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Saving extends Model
{
    protected $fillable = [
        'relationship_id',
        'title',
        'target_amount',
        'current_amount',
        'icon',
    ];

    public function relationship(): BelongsTo
    {
        return $this->belongsTo(Relationship::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(SavingLog::class)->latest();
    }

    public function getProgressAttribute(): float
    {
        if ($this->target_amount <= 0) return 0;
        return min(100, ($this->current_amount / $this->target_amount) * 100);
    }
}
