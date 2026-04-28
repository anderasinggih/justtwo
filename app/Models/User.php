<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
    ];

    /**
     * Get the URL to the user's profile photo.
     */
    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path
            ? \Illuminate\Support\Facades\Storage::url($this->profile_photo_path)
            : "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&color=7F9CF5&background=EBF4FF";
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the relationship the user belongs to.
     */
    public function relationship()
    {
        return $this->hasOneThrough(
            Relationship::class,
            RelationshipMember::class,
            'user_id',
            'id',
            'id',
            'relationship_id'
        );
    }

    /**
     * Get the relationship member record for the user.
     */
    public function relationshipMember()
    {
        return $this->hasOne(RelationshipMember::class);
    }

    /**
     * Get the posts created by the user.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
