<?php

namespace App\Livewire;

use App\Models\PostMedia;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Gallery extends Component
{
    public $isSelecting = false;
    public $selectedMedia = [];

    public function archiveSelected($ids = null)
    {
        $targetIds = $ids ?? $this->selectedMedia;
        
        if (empty($targetIds)) return;

        $mediaItems = PostMedia::whereIn('id', $targetIds)->get();
        
        foreach ($mediaItems as $media) {
            $post = $media->post;
            if ($post && $post->relationship_id === Auth::user()->relationship_id) {
                $post->update([
                    'is_archived' => true,
                    'archived_at' => now(),
                ]);
            }
        }

        $this->selectedMedia = [];
        $this->isSelecting = false;
        
        $this->dispatch('media-archived');
    }

    public function render()
    {
        $relationship = Auth::user()->relationship;
        
        $media = PostMedia::whereHas('post', function($q) use ($relationship) {
            $q->where('relationship_id', $relationship->id)
              ->where('is_archived', false);
        })
        ->orderBy('captured_at', 'desc')
        ->get();

        $groupedMedia = $media->groupBy(function($item) {
            return $item->captured_at ? $item->captured_at->format('Y-F') : $item->created_at->format('Y-F');
        });

        return view('livewire.gallery', [
            'groupedMedia' => $groupedMedia,
        ])->layout('layouts.app');
    }
}
