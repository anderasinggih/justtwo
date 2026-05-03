<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanItinerary extends Model
{
    protected $fillable = [
        'plan_id',
        'event_date',
        'event_time',
        'activity',
        'notes',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
