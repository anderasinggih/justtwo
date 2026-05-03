<?php

namespace App\Livewire;

use App\Models\PostMedia;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class InternalGalleryPreview extends Component
{
    public $allMedia = [];
    public $initialMediaIndex = 0;
    public $targetId;
    public $theme = 'light';

    public function mount($media)
    {
        $targetMedia = \App\Models\PostMedia::findOrFail($media);
        $relationship = Auth::user()->relationship;
        $relationshipId = Auth::user()->relationshipMember->relationship_id ?? null;

        // Security check
        if ($targetMedia->post?->user_id === Auth::id()) {
            // Owner always has access
        } elseif ($relationshipId && $targetMedia->post?->relationship_id === $relationshipId) {
            // Same relationship has access
        } else {
            abort(403, "Access Denied. Post Rel: " . ($targetMedia->post?->relationship_id ?? 'NULL') . ", Your Rel: " . ($relationshipId ?? 'NULL'));
        }

        $this->targetId = $targetMedia->post_id;
        
        // Load all media in this relationship
        $mediaRecords = \App\Models\PostMedia::whereHas('post', function ($query) use ($relationshipId) {
            $query->where(function($q) use ($relationshipId) {
                if ($relationshipId) {
                    $q->where('relationship_id', $relationshipId);
                }
                $q->orWhere('user_id', Auth::id());
            })->where('is_archived', false);
        })
        ->with(['post', 'post.user'])
        ->orderBy('captured_at', 'desc')
        ->orderBy('created_at', 'desc')
        ->get();

        $mediaList = [];
        foreach ($mediaRecords as $m) {
            if ($m->id == $targetMedia->id) {
                $this->initialMediaIndex = count($mediaList);
            }
            
            $mediaList[] = [
                'id' => $m->id,
                'post_id' => $m->post_id,
                'file_path' => \Storage::disk('public')->url($m->file_path_original),
                'file_type' => $m->file_type,
                'location' => $m->location ?: ($m->post?->location ?? ''),
                'lat' => $m->lat,
                'lon' => $m->lon,
                'captured_at' => $m->captured_at ? $m->captured_at->toISOString() : null,
                'date' => ($m->captured_at ?: ($m->post?->created_at ?? now()))->format('l, j M Y'),
                'time' => ($m->captured_at ?: ($m->post?->created_at ?? now()))->format('g:i A'),
                'user_id' => $m->post?->user_id
            ];
        }

        $this->allMedia = $mediaList;
        $this->theme = 'dark';
    }

    public function toggleReaction($postId)
    {
        $post = \App\Models\Post::findOrFail($postId);
        $userId = Auth::id();

        $reaction = $post->reactions()->where('user_id', $userId)->where('type', 'heart')->first();

        if ($reaction) {
            $reaction->delete();
        } else {
            $post->reactions()->create([
                'relationship_id' => $post->relationship_id,
                'user_id' => $userId,
                'type' => 'heart',
            ]);
        }
    }

    public function archiveMedia($mediaId)
    {
        $media = \App\Models\PostMedia::findOrFail($mediaId);
        $post = $media->post;
        
        if (Auth::id() !== $post->user_id) {
            return;
        }

        $post->update([
            'is_archived' => true,
            'archived_at' => now(),
        ]);

        $this->allMedia = array_values(array_filter($this->allMedia, fn($m) => $m['id'] !== $mediaId));
        $this->dispatch('media-archived');
    }

    public function render()
    {
        return view('livewire.public-post-preview', [
            'allMedia' => $this->allMedia,
            'initialMediaIndex' => $this->initialMediaIndex,
            'theme' => $this->theme,
        ])->layout('layouts.guest', ['theme' => $this->theme]);
    }
}
