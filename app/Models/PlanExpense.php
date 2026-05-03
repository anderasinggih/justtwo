<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanExpense extends Model
{
    protected $fillable = [
        'plan_id',
        'title',
        'amount',
        'category',
        'is_paid',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
