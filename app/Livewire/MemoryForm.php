<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\PostMedia;
use App\Notifications\PartnerAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class MemoryForm extends Component
{
    use WithFileUploads;

    public $content;
    public $title;
    public $mood;
    public $photos = [];
    public $type = 'photo'; // Default type

    protected $rules = [
        'content' => 'required|string|min:5',
        'title' => 'nullable|string|max:255',
        'mood' => 'nullable|string|max:50',
        'photos.*' => 'image|max:5120', // 5MB max each
        'type' => 'required|in:photo,gallery,story,milestone,anniversary,travel,note',
    ];


    public function save()
    {
        $this->validate();

        $post = Post::create([
            'user_id' => Auth::id(),
            'relationship_id' => Auth::user()->relationship->id,
            'type' => $this->type,
            'title' => $this->title,
            'content' => $this->content,
            'mood' => $this->mood,
            'published_at' => now(),
        ]);


        foreach ($this->photos as $index => $photo) {
            // 1. Store raw file in local temp storage (not public)
            $tempPath = $photo->store('temp-uploads', 'local');
            $originalName = $photo->getClientOriginalName();

            // 2. Create placeholder PostMedia record
            $media = PostMedia::create([
                'post_id' => $post->id,
                'original_file_name' => $originalName,
                'file_type' => 'processing',
                'sort_order' => $index,
            ]);

            // 3. Dispatch processing job
            \App\Jobs\ProcessMediaJob::dispatch($media->id, $tempPath, $originalName);
        }


        $partner = Auth::user()->relationship->users()->where('users.id', '!=', Auth::id())->first();
        if ($partner) {
            $partner->notify(new PartnerAction('post', Auth::user()->name, "shared a new {$this->type}"));
        }

        $this->reset(['content', 'title', 'photos', 'type', 'mood']);

        $this->dispatch('postCreated');
        session()->flash('success', 'memory shared successfully.');
    }

    public function render()
    {
        return view('livewire.memory-form');
    }
}
