<?php

namespace App\Livewire;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Journal extends Component
{
    public $content;
    public $title;
    public $mood = '❤️';
    public $search = '';

    public function saveEntry()
    {
        $this->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|min:10',
            'mood' => 'required|string',
        ]);

        Post::create([
            'user_id' => Auth::id(),
            'relationship_id' => Auth::user()->relationship->id,
            'type' => 'journal',
            'title' => $this->title,
            'content' => $this->content,
            'mood' => $this->mood,
            'published_at' => now(),
        ]);

        $this->reset(['content', 'title', 'mood']);
        session()->flash('success', 'journal entry saved.');
    }


    public function render()
    {
        $entries = Auth::user()->relationship->posts()
            ->where('type', 'journal')
            ->when($this->search, function($q) {
                $q->where('content', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->get();

        return view('livewire.journal', [
            'entries' => $entries,
        ])->layout('layouts.app');
    }
}
