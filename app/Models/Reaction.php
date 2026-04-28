<?php

namespace App\Models;

use App\Traits\BelongsToRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    use HasFactory, BelongsToRelationship;

    protected $fillable = [
        'relationship_id',
        'post_id',
        'user_id',
        'type',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
