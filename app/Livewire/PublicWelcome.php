<?php

namespace App\Livewire;

use App\Models\PublicSetting;
use App\Models\Relationship;
use Livewire\Component;

class PublicWelcome extends Component
{
    public $theme = 'light';

    public function mount()
    {
        $settings = PublicSetting::getSettings();
        $this->theme = $settings->theme ?? 'light';
    }

    public function render()
    {
        $settings = PublicSetting::getSettings();
        $banners = $settings->banner_paths ?? [];
        $bannerData = $settings->banner_data ?? [];
        $relationship = Relationship::orderBy('id', 'desc')->first();
        $spaceName = $relationship?->name ?? 'justtwo';
        $anniversaryDate = $relationship?->anniversary_date;

        $journeyVideoId = null;
        if ($settings->journey_video_url) {
            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $settings->journey_video_url, $match);
            $journeyVideoId = $match[1] ?? null;
        }

        $journeyVideoId2 = null;
        if ($settings->journey_video_url_2) {
            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $settings->journey_video_url_2, $match);
            $journeyVideoId2 = $match[1] ?? null;
        }

        $spotifyEmbedUrl = null;
        if ($settings->spotify_url) {
            if (preg_match('/spotify\.com\/(playlist|track|album|artist)\/([a-zA-Z0-9]+)/', $settings->spotify_url, $matches)) {
                $spotifyEmbedUrl = "https://open.spotify.com/embed/{$matches[1]}/{$matches[2]}?utm_source=generator&autoplay=1";
            }
        }

        return view('livewire.public-welcome', [
            'settings' => $settings,
            'banners' => $banners,
            'bannerData' => $bannerData,
            'spaceName' => $spaceName,
            'anniversaryDate' => $anniversaryDate,
            'journeyVideoId' => $journeyVideoId,
            'journeyVideoId2' => $journeyVideoId2,
            'spotifyEmbedUrl' => $spotifyEmbedUrl,
        ])->layout('layouts.public', ['theme' => $this->theme]);
    }
}
