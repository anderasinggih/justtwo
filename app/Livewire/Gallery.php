<?php

namespace App\Livewire;

use App\Models\PostMedia;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Gallery extends Component
{
    public function render()
    {
        $relationship = Auth::user()->relationship;
        
        $media = PostMedia::whereHas('post', function($q) use ($relationship) {
            $q->where('relationship_id', $relationship->id)
              ->where('is_archived', false);
        })
        ->orderBy('captured_at', 'desc')
        ->orderBy('created_at', 'desc')
        ->get();

        $groupedMedia = $media->groupBy(function($item) {
            $date = $item->captured_at ?? $item->created_at;
            return $date->format('Y-F');
        });

        return view('livewire.gallery', [
            'groupedMedia' => $groupedMedia,
        ])->layout('layouts.app');
    }
}
