<?php

namespace App\Livewire;

use Livewire\Component;

class PublicPostPreview extends Component
{
    public $allMedia = [];
    public $initialMediaIndex = 0;
    public $targetId;
    public $theme = 'light';

    public function mount(\App\Models\Post $post)
    {
        if (!$post->is_public || $post->is_archived) {
            abort(404);
        }
        
        $this->targetId = $post->id;
        
        // Load all public posts in natural order
        $posts = \App\Models\Post::where('is_public', true)
            ->where('is_archived', false)
            ->with(['user', 'media', 'reactions'])
            ->latest()
            ->get();
        
        $targetMediaId = request()->query('media');
        $mediaList = [];
        $foundTarget = false;
        
        foreach ($posts as $p) {
            foreach ($p->media as $m) {
                if (!$foundTarget) {
                    if ($targetMediaId) {
                        if ($m->id == $targetMediaId) {
                            $this->initialMediaIndex = count($mediaList);
                            $foundTarget = true;
                        }
                    } else if ($p->id == $this->targetId) {
                        $this->initialMediaIndex = count($mediaList);
                        $foundTarget = true;
                    }
                }
                $mediaList[] = [
                    'id' => $m->id,
                    'post_id' => $p->id,
                    'file_path' => \Storage::disk('public')->url($m->file_path_original),
                    'file_type' => $m->file_type,
                    'location' => $m->location ?: $p->location,
                    'lat' => $m->lat,
                    'lon' => $m->lon,
                    'captured_at' => $m->captured_at ? $m->captured_at->toISOString() : null,
                    'date' => ($m->captured_at ?: $p->created_at)->format('l, j M Y'),
                    'time' => ($m->captured_at ?: $p->created_at)->format('g:i A'),
                    'user_id' => $p->user_id
                ];
            }
        }
        $this->allMedia = $mediaList;
        
        $settings = \App\Models\PublicSetting::first();
        $this->theme = $settings->theme ?? 'light';
    }

    public function toggleReaction($postId)
    {
        $post = \App\Models\Post::findOrFail($postId);
        
        // Use session for guest tracking
        $guestId = session()->getId();
        $userId = \Illuminate\Support\Facades\Auth::id();

        $query = $post->reactions()->where('type', 'heart');
        
        if ($userId) {
            $reaction = $query->where('user_id', $userId)->first();
        } else {
            $reaction = $query->where('guest_id', $guestId)->first();
        }

        if ($reaction) {
            $reaction->delete();
        } else {
            // Check if database supports guest_id yet, fallback to anonymous if needed
            try {
                $post->reactions()->create([
                    'relationship_id' => $post->relationship_id,
                    'user_id' => $userId,
                    'guest_id' => $guestId,
                    'type' => 'heart',
                ]);
            } catch (\Exception $e) {
                // Fallback for missing column during migration issues
                if ($userId) {
                    $post->reactions()->create([
                        'relationship_id' => $post->relationship_id,
                        'user_id' => $userId,
                        'type' => 'heart',
                    ]);
                }
            }
        }
    }

    public function archiveMedia($mediaId)
    {
        $media = \App\Models\PostMedia::findOrFail($mediaId);
        $post = $media->post;
        
        if (\Illuminate\Support\Facades\Auth::id() !== $post->user_id) {
            return;
        }

        $post->update([
            'is_archived' => true,
            'archived_at' => now()
        ]);

        // Remove from the local array to update UI immediately
        $this->allMedia = array_values(array_filter($this->allMedia, fn($m) => $m['id'] !== $mediaId));
        
        $this->dispatch('media-archived');
    }

    public function render()
    {
        return view('livewire.public-post-preview')
            ->layout('layouts.public', ['theme' => $this->theme]);
    }
}
