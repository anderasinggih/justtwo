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
        $relationshipId = Auth::user()->relationshipMember?->relationship_id;
        
        foreach ($mediaItems as $media) {
            $post = $media->post;
            if ($post && $post->relationship_id === $relationshipId) {
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
        $relationshipId = Auth::user()->relationshipMember?->relationship_id;
        
        $media = PostMedia::whereHas('post', function($q) use ($relationshipId) {
            $q->where('relationship_id', $relationshipId)
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
