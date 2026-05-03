<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavingLog extends Model
{
    protected $fillable = [
        'saving_id',
        'user_id',
        'amount',
        'note',
    ];

    public function savingGoal(): BelongsTo
    {
        return $this->belongsTo(Saving::class, 'saving_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
