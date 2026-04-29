<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'theme',
        'hero_title',
        'hero_subtitle',
        'about_us',
        'youtube_url',
        'journey_title',
        'journey_description',
        'journey_video_url',
        'journey_video_url_2',
        'spotify_url',
        'banner_paths',
        'banner_data',
    ];

    protected $casts = [
        'banner_paths' => 'array',
        'banner_data' => 'array',
    ];

    public static function getSettings()
    {
        return self::find(1) ?? self::first() ?? new self([
            'hero_title' => 'capturing the moments that define our journey.',
            'hero_subtitle' => 'a small window into our private gallery.',
            'theme' => 'light',
            'banner_paths' => []
        ]);
    }
}
