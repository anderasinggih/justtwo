<?php

namespace App\Livewire;

use Livewire\Component;

class PublicPostPreview extends Component
{
    public $posts;
    public $targetId;
    public $theme = 'light';

    public function mount(\App\Models\Post $post)
    {
        if (!$post->is_public || $post->is_archived) {
            abort(404);
        }
        
        $this->targetId = $post->id;
        
        // Load all public posts in natural order
        $this->posts = \App\Models\Post::where('is_public', true)
            ->where('is_archived', false)
            ->with(['user', 'media'])
            ->latest()
            ->get();
        
        $settings = \App\Models\PublicSetting::first();
        $this->theme = $settings->theme ?? 'light';
    }

    public function render()
    {
        return view('livewire.public-post-preview')
            ->layout('layouts.public', ['theme' => $this->theme]);
    }
}
