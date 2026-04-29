<?php

namespace App\Livewire;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ArchivedPosts extends Component
{
    use WithPagination;

    public function restorePost($postId)
    {
        $post = Post::findOrFail($postId);
        if ($post->user_id === Auth::id() || $post->relationship_id === Auth::user()->relationship_id) {
            $post->update(['is_archived' => false]);
            $this->dispatch('postRestored');
        }
    }

    public function deletePost($postId)
    {
        $post = Post::findOrFail($postId);
        if ($post->user_id === Auth::id()) {
            $post->delete();
            $this->dispatch('postDeleted');
        }
    }

    public function render()
    {
        $posts = Auth::user()->relationship->posts()
            ->where('is_archived', true)
            ->with(['user', 'media'])
            ->latest()
            ->paginate(12);

        return view('livewire.archived-posts', [
            'posts' => $posts,
        ])->layout('layouts.app');
    }
}
