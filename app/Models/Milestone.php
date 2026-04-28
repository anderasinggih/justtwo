<?php

namespace App\Models;

use App\Traits\BelongsToRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use HasFactory, BelongsToRelationship;

    protected $fillable = [
        'relationship_id',
        'title',
        'event_date',
        'description',
        'category',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function relationship()
    {
        return $this->belongsTo(Relationship::class);
    }
}
