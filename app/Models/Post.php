<?php

namespace App\Models;

use App\Traits\BelongsToRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory, BelongsToRelationship;

    protected $fillable = [
        'relationship_id',
        'user_id',
        'type',
        'title',
        'content',
        'mood',
        'location',
        'is_pinned',
        'is_archived',
        'published_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_archived' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function relationship()
    {
        return $this->belongsTo(Relationship::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->hasMany(PostMedia::class)->orderBy('sort_order');
    }


    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }
}
