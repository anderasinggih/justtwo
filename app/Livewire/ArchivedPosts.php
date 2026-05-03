<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\PostMedia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ArchivedPosts extends Component
{
    public $isSelecting = false;
    public $selectedMedia = [];

    public function restoreSelected($ids = null)
    {
        $targetIds = $ids ?? $this->selectedMedia;
        if (empty($targetIds)) return;

        $mediaItems = PostMedia::whereIn('id', $targetIds)->get();
        $relationshipId = Auth::user()->relationshipMember?->relationship_id;

        foreach ($mediaItems as $media) {
            $post = $media->post;
            if ($post && ($post->relationship_id === $relationshipId)) {
                $post->update(['is_archived' => false, 'archived_at' => null]);
            }
        }

        $this->isSelecting = false;
        $this->selectedMedia = [];
        $this->dispatch('media-restored');
    }

    public function deleteSelectedPermanently($ids = null)
    {
        $targetIds = $ids ?? $this->selectedMedia;
        if (empty($targetIds)) return;

        $mediaItems = PostMedia::whereIn('id', $targetIds)->get();
        $relationshipId = Auth::user()->relationshipMember?->relationship_id;

        foreach ($mediaItems as $media) {
            $post = $media->post;
            if ($post && $post->relationship_id === $relationshipId) {
                Storage::disk('public')->delete($media->file_path_original);
                if ($media->file_path_thumbnail) {
                    Storage::disk('public')->delete($media->file_path_thumbnail);
                }
                $media->delete();
                
                if ($post->media()->count() === 0) {
                    $post->delete();
                }
            }
        }

        $this->isSelecting = false;
        $this->selectedMedia = [];
        $this->dispatch('media-deleted-permanently');
    }

    public function render()
    {
        $relationshipId = Auth::user()->relationshipMember?->relationship_id;
        
        $media = PostMedia::join('posts', 'post_media.post_id', '=', 'posts.id')
            ->where('posts.relationship_id', $relationshipId)
            ->where('posts.is_archived', true)
            ->orderBy('posts.archived_at', 'desc')
            ->select('post_media.*', 'posts.archived_at')
            ->get();

        $groupedMedia = $media->groupBy(function($item) {
            $date = $item->archived_at ?? $item->created_at;
            return $date->format('Y-F');
        });

        return view('livewire.archived-posts', [
            'groupedMedia' => $groupedMedia,
        ])->layout('layouts.app');
    }
}
