<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relationship extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($relationship) {
            $relationship->invite_code = \Illuminate\Support\Str::random(10);
        });
    }


    protected $fillable = [
        'name',
        'creator_id',
        'invite_code',
        'anniversary_date',
        'cover_photo_path',
        'theme',
    ];

    protected $casts = [
        'anniversary_date' => 'date',
    ];

    public function getDaysTogetherAttribute()
    {
        if (!$this->anniversary_date) return 0;
        return (int) abs(now()->diffInDays($this->anniversary_date));
    }


    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function members()
    {
        return $this->hasMany(RelationshipMember::class);
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, RelationshipMember::class, 'relationship_id', 'id', 'id', 'user_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    public static function formatNumber($number)
    {
        if ($number >= 1000000) {
            return round($number / 1000000, 1) . 'jt';
        }
        if ($number >= 1000) {
            return round($number / 1000, 1) . 'k';
        }
        return $number;
    }
}
