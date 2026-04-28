<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationshipMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'relationship_id',
        'user_id',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function relationship()
    {
        return $this->belongsTo(Relationship::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
