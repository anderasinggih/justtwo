<?php

namespace App\Livewire;

use Livewire\Component;

class PublicPostPreview extends Component
{
    public $posts;
    public $targetId;
    public $theme = 'light';

    public function mount(\App\Models\Post $post)
    {
        if (!$post->is_public || $post->is_archived) {
            abort(404);
        }
        
        $this->targetId = $post->id;
        
        // Load all public posts in natural order
        $this->posts = \App\Models\Post::where('is_public', true)
            ->where('is_archived', false)
            ->with(['user', 'media', 'reactions', 'comments' => function($q) {
                $q->whereNull('parent_id')->with(['user', 'replies.user'])->latest();
            }])
            ->latest()
            ->get();
        
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

    public function deletePost($postId)
    {
        $post = \App\Models\Post::findOrFail($postId);
        if (\Illuminate\Support\Facades\Auth::id() === $post->user_id) {
            $post->delete();
            return redirect()->route('welcome');
        }
    }

    public function render()
    {
        return view('livewire.public-post-preview')
            ->layout('layouts.public', ['theme' => $this->theme]);
    }
}
