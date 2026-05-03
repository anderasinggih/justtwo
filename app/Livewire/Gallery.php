<?php

namespace App\Livewire;

use App\Models\PostMedia;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Gallery extends Component
{
    public $isSelecting = false;
    public $selectedMedia = [];

    public function toggleSelection()
    {
        $this->isSelecting = !$this->isSelecting;
        $this->selectedMedia = [];
    }

    public function selectMedia($id)
    {
        if (in_array($id, $this->selectedMedia)) {
            $this->selectedMedia = array_diff($this->selectedMedia, [$id]);
        } else {
            $this->selectedMedia[] = $id;
        }
    }

    public function archiveSelected()
    {
        if (empty($this->selectedMedia)) return;

        $mediaItems = PostMedia::whereIn('id', $this->selectedMedia)->get();
        
        $relationshipId = Auth::user()->relationship_id;
        
        foreach ($mediaItems as $media) {
            $post = $media->post;
            if ($post && $post->relationship_id === $relationshipId) {
                $post->update([
                    'is_archived' => true,
                    'archived_at' => now(),
                ]);
            }
        }

        $this->isSelecting = false;
        $this->selectedMedia = [];
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
