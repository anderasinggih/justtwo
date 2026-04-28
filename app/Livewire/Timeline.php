<?php

namespace App\Livewire;

use App\Models\Post;
use App\Notifications\PartnerAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Timeline extends Component
{
    use WithPagination;

    public $search = '';

    protected $listeners = ['postCreated' => '$refresh'];

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

    public function addComment($postId, $content)
    {
        if (empty($content)) return;

        $post = Post::findOrFail($postId);
        $post->comments()->create([
            'user_id' => Auth::id(),
            'relationship_id' => $post->relationship_id,
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
        $posts = Auth::user()->relationship->posts()
            ->where('is_archived', false)
            ->when($this->search, function($q) {
                $q->where(function($qq) {
                    $qq->where('content', 'like', '%' . $this->search . '%')
                       ->orWhere('title', 'like', '%' . $this->search . '%');
                });
            })
            ->with(['user', 'media', 'reactions', 'comments.user'])
            ->latest()
            ->paginate(10);

        return view('livewire.timeline', [
            'posts' => $posts,
        ])->layout('layouts.app');
    }
}
