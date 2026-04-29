<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\PublicSetting;
use Livewire\Component;
use Livewire\WithPagination;

class PublicAlbumDetail extends Component
{
    use WithPagination;

    public $year;
    public $month;
    public $theme = 'light';

    public function mount($year, $month)
    {
        $this->year = $year;
        $this->month = $month;
        
        $settings = PublicSetting::first();
        $this->theme = $settings->theme ?? 'light';
    }

    public function render()
    {
        // Convert month name to number
        $monthNumber = date('m', strtotime($this->month));

        $posts = Post::where('is_public', true)
            ->where('is_archived', false)
            ->where('is_secret', false)
            ->whereYear('created_at', $this->year)
            ->whereMonth('created_at', $monthNumber)
            ->with(['user', 'media', 'reactions'])
            ->latest()
            ->paginate(24);

        return view('livewire.public-album-detail', [
            'posts' => $posts,
            'monthName' => $this->month,
        ])->layout('layouts.public', ['theme' => $this->theme]);
    }
}
