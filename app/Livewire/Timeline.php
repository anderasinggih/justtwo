<?php

namespace App\Livewire;

use App\Models\Post;
use App\Notifications\PartnerAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Timeline extends Component
{
    use WithPagination;

    public $search = '';

    #[Url]
    public $post = null;

    #[Url]
    public $index = 0;

    protected $listeners = [
        'postCreated' => '$refresh',
        'postDeleted' => '$refresh'
    ];

    public function toggleReaction($postId)
    {
        $post = Post::findOrFail($postId);
        $reaction = $post->reactions()->where('user_id', Auth::id())->first();

        if ($reaction) {
            $reaction->delete();
        } else {
            $post->reactions()->create([
                'user_id' => Auth::id(),
                'relationship_id' => $post->relationship_id,
                'type' => 'heart',
            ]);

            $partner = Auth::user()->relationship->users()->where('users.id', '!=', Auth::id())->first();
            if ($partner) {
                $partner->notify(new PartnerAction('reaction', Auth::user()->name, 'loved your memory'));
            }
        }
    }

    public function archivePost($postId)
    {
        $post = Post::findOrFail($postId);
        $post->update(['is_archived' => !$post->is_archived]);
    }

    public function deletePost($postId)
    {
        $post = Post::findOrFail($postId);
        // Only allow owner to delete
        if ($post->user_id === Auth::id()) {
            $post->delete();
            $this->dispatch('postDeleted');
            session()->flash('success', 'memory deleted successfully.');
        }
    }

    public function toggleBookmark($postId)
    {
        $post = Post::findOrFail($postId);
        $bookmark = $post->bookmarks()->where('user_id', Auth::id())->first();

        if ($bookmark) {
            $bookmark->delete();
        } else {
            $post->bookmarks()->create([
                'user_id' => Auth::id(),
            ]);
        }
    }

    public function addComment($postId, $content, $parentId = null)
    {
        if (empty($content)) return;

        $post = Post::findOrFail($postId);
        $post->comments()->create([
            'user_id' => Auth::id(),
            'relationship_id' => $post->relationship_id,
            'parent_id' => $parentId,
            'content' => $content,
        ]);

        $partner = Auth::user()->relationship->users()->where('users.id', '!=', Auth::id())->first();
        if ($partner) {
            $partner->notify(new PartnerAction('comment', Auth::user()->name, 'commented on your memory'));
        }

        $this->dispatch('commentAdded');
    }

    public function render()
    {
        $query = Auth::user()->relationship->posts()
            ->where('is_archived', false);

        if ($this->post) {
            $requestedPost = Post::find($this->post);
            if ($requestedPost) {
                $query->where('created_at', '<=', $requestedPost->created_at);
            }
        }

        $posts = $query->when($this->search, function($q) {
                $q->where(function($qq) {
                    $qq->where('content', 'like', '%' . $this->search . '%')
                       ->orWhere('title', 'like', '%' . $this->search . '%');
                });
            })
            ->with(['user', 'media', 'reactions', 'bookmarks', 'comments' => function($q) {
                $q->whereNull('parent_id')->with(['user', 'replies.user'])->latest();
            }])
            ->latest()
            ->paginate(10);

        return view('livewire.timeline', [
            'posts' => $posts,
        ])->layout('layouts.app');
    }
}
