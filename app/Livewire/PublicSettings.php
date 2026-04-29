<?php

namespace App\Livewire;

use App\Models\PublicSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;
use Livewire\WithFileUploads;

class PublicSettings extends Component
{
    use WithFileUploads;

    public $hero_title;
    public $hero_subtitle;
    public $about_us;
    public $theme = 'light';
    public $new_banners = [];
    public $existing_banners = [];
    public $banner_titles = [];
    public $banner_subtitles = [];
    public $status_general = '';
    public $status_banners = '';
    public $youtube_url;
    public $journey_video_url;
    public $journey_video_url_2;

    public function mount()
    {
        $this->authorizeAccess();

        $settings = PublicSetting::getSettings();
        
        $this->hero_title = $settings->hero_title;
        $this->hero_subtitle = $settings->hero_subtitle;
        $this->about_us = $settings->about_us;
        $this->youtube_url = $settings->youtube_url;
        $this->journey_video_url = $settings->journey_video_url;
        $this->journey_video_url_2 = $settings->journey_video_url_2;
        $this->theme = $settings->theme ?? 'light';
        $this->existing_banners = $settings->banner_paths ?? [];
        
        $data = $settings->banner_data ?? [];
        for ($i = 0; $i < 5; $i++) {
            $this->banner_titles[$i] = $data[$i]['title'] ?? '';
            $this->banner_subtitles[$i] = $data[$i]['subtitle'] ?? '';
        }
    }

    protected function authorizeAccess()
    {
        $user = Auth::user();
        if (!$user || !$user->relationship) {
            abort(403);
        }
    }

    public function removeExistingBanner($index)
    {
        // Get all current data combined for shifting
        $allPaths = $this->existing_banners;
        $allTitles = $this->banner_titles;
        $allSubtitles = $this->banner_subtitles;

        unset($allPaths[$index]);
        unset($allTitles[$index]);
        unset($allSubtitles[$index]);

        $this->existing_banners = array_values($allPaths);
        
        // Re-index titles/subtitles for all 5 slots
        $newTitles = [];
        $newSubtitles = [];
        $tempTitles = array_values($allTitles);
        $tempSubtitles = array_values($allSubtitles);
        
        for ($i = 0; $i < 5; $i++) {
            $newTitles[$i] = $tempTitles[$i] ?? '';
            $newSubtitles[$i] = $tempSubtitles[$i] ?? '';
        }
        
        $this->banner_titles = $newTitles;
        $this->banner_subtitles = $newSubtitles;
    }

    public function removeNewBanner($index)
    {
        unset($this->new_banners[$index]);
        // Note: we don't re-index new_banners because they are indexed by their input position 0-4
    }

    // SIMPAN KHUSUS TEKS & TEMA
    public function saveGeneral()
    {
        $this->status_general = 'saving...';
        
        $this->validate([
            'hero_title' => 'required|string|max:255',
            'hero_subtitle' => 'nullable|string|max:1000',
            'about_us' => 'nullable|string|max:2000',
            'youtube_url' => 'nullable|url|max:255',
            'journey_video_url' => 'nullable|url|max:255',
            'journey_video_url_2' => 'nullable|url|max:255',
            'theme' => 'required|in:light,dark,rose,midnight',
        ]);

        try {
            \Illuminate\Support\Facades\DB::table('public_settings')->updateOrInsert(
                ['id' => 1],
                [
                    'hero_title' => $this->hero_title,
                    'hero_subtitle' => $this->hero_subtitle,
                    'about_us' => $this->about_us,
                    'youtube_url' => $this->youtube_url,
                    'journey_video_url' => $this->journey_video_url,
                    'journey_video_url_2' => $this->journey_video_url_2,
                    'theme' => $this->theme,
                    'updated_at' => now(),
                ]
            );

            $this->status_general = 'text & theme saved!';
            Artisan::call('view:clear');
            Artisan::call('cache:clear');

        } catch (\Exception $e) {
            $this->status_general = 'error: ' . $e->getMessage();
        }
    }

    // SIMPAN KHUSUS BANNER GAMBAR
    public function saveBanners()
    {
        $this->status_banners = 'uploading banners...';

        $this->validate([
            'new_banners.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,mov,webm,ogg|max:102400',
        ]);

        if (count($this->existing_banners) + count($this->new_banners) > 5) {
            $this->status_banners = 'error: maximum 5 banners allowed.';
            return;
        }

        try {
            $uploadedPaths = [];
            if (!empty($this->new_banners)) {
                foreach ($this->new_banners as $banner) {
                    $filename = 'banner_' . time() . '_' . uniqid() . '.' . $banner->getClientOriginalExtension();
                    Storage::disk('public')->put('banners/' . $filename, file_get_contents($banner->getRealPath()));
                    $uploadedPaths[] = 'banners/' . $filename;
                }
            }

            $finalBanners = array_merge($this->existing_banners, $uploadedPaths);
            $finalBanners = array_slice($finalBanners, 0, 5);

            $bannerData = [];
            for ($i = 0; $i < 5; $i++) {
                $bannerData[$i] = [
                    'title' => $this->banner_titles[$i] ?? '',
                    'subtitle' => $this->banner_subtitles[$i] ?? '',
                ];
            }

            \Illuminate\Support\Facades\DB::table('public_settings')->updateOrInsert(
                ['id' => 1],
                [
                    'banner_paths' => json_encode($finalBanners),
                    'banner_data' => json_encode($bannerData),
                    'updated_at' => now(),
                ]
            );

            $this->existing_banners = $finalBanners;
            $this->new_banners = [];
            $this->status_banners = 'banners updated!';
            
            Artisan::call('view:clear');
            Artisan::call('cache:clear');

        } catch (\Exception $e) {
            $this->status_banners = 'error: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.public-settings')
            ->layout('layouts.app');
    }
}
