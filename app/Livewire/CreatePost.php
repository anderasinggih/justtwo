<?php

namespace App\Livewire;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreatePost extends Component
{
    use WithFileUploads;

    public $step = 1; // 1: Select, 2: Preview/Caption
    public $photos = [];
    public $caption = '';
    public $location = '';
    public $mood = 'happy';

    public function updatedPhotos()
    {
        $this->validate([
            'photos.*' => 'image|max:10240', // 10MB max
        ]);
        $this->step = 2;
    }

    public function submit()
    {
        $this->validate([
            'photos' => 'required|array|min:1',
            'caption' => 'required|string',
        ]);

        $post = Auth::user()->relationship->posts()->create([
            'user_id' => Auth::id(),
            'title' => $this->caption,
            'content' => $this->caption,
            'location' => $this->location,
            'mood' => $this->mood,
            'type' => 'memory',
            'published_at' => now(),
        ]);

        foreach ($this->photos as $photo) {
            $path = $photo->store('memories', 'public');
            $post->media()->create([
                'file_path_original' => $path,
                'file_path_thumbnail' => $path,
                'file_type' => $photo->getMimeType(),
                'file_size_kb' => $photo->getSize() / 1024,
            ]);
        }

        return redirect()->route('timeline');
    }

    public function render()
    {
        return view('livewire.create-post')
            ->layout('layouts.app');
    }
}
