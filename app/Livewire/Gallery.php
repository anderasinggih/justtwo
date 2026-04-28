<?php

namespace App\Livewire;

use App\Models\PostMedia;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Gallery extends Component
{
    public function render()
    {
        $media = PostMedia::whereHas('post', function($q) {
            $q->where('relationship_id', Auth::user()->relationship->id);
        })->latest()->get();

        return view('livewire.gallery', [
            'media' => $media,
        ])->layout('layouts.app');
    }
}
