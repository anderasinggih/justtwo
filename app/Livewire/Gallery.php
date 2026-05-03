<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\PostMedia;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Gallery extends Component
{
    public $isSelecting = false;
    public $selectedMedia = [];
    public $showConfirmModal = false;

    public function confirmDelete()
    {
        if (empty($this->selectedMedia)) return;
        $this->showConfirmModal = true;
    }

    public function cancelDelete()
    {
        $this->showConfirmModal = false;
    }

    public function archiveSelected()
    {
        if (empty($this->selectedMedia)) return;

        $postIds = PostMedia::whereIn('id', $this->selectedMedia)
            ->pluck('post_id')
            ->toArray();

        if (!empty($postIds)) {
            Post::whereIn('id', $postIds)
                ->where('relationship_id', Auth::user()->relationship_id)
                ->update([
                    'is_archived' => true,
                    'archived_at' => now(),
                ]);
        }

        $this->selectedMedia = [];
        $this->isSelecting = false;
        $this->showConfirmModal = false;
        
        $this->dispatch('media-archived');
    }

    public function render()
    {
        $relationship = Auth::user()->relationship;
        
        if (!$relationship) {
            return view('livewire.gallery', ['groupedMedia' => collect()])->layout('layouts.app');
        }

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
