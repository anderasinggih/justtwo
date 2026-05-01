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
    public $theme = 'light';

    public function mount($media)
    {
        $relationship = Auth::user()->relationship;
        $targetMedia = PostMedia::findOrFail($media);

        // Security check
        if ($targetMedia->post->relationship_id !== $relationship->id) {
            abort(403);
        }

        // Load all media in the relationship
        $mediaRecords = PostMedia::whereHas('post', function ($query) use ($relationship) {
            $query->where('relationship_id', $relationship->id)
                ->where('is_archived', false);
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
                'location' => $m->location ?: $m->post->location,
                'lat' => $m->lat,
                'lon' => $m->lon,
                'captured_at' => $m->captured_at ? $m->captured_at->toISOString() : null,
                'date' => ($m->captured_at ?: $m->post->created_at)->format('l, j M Y'),
                'time' => ($m->captured_at ?: $m->post->created_at)->format('g:i A'),
                'user_id' => $m->post->user_id
            ];
        }

        $this->allMedia = $mediaList;
        $this->theme = $relationship->theme ?? 'light';
    }

    public function toggleReaction($postId)
    {
        $post = Post::findOrFail($postId);
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

    public function deleteMedia($mediaId)
    {
        $media = PostMedia::findOrFail($mediaId);
        $post = $media->post;
        
        if (Auth::id() !== $post->user_id) {
            return;
        }

        \Illuminate\Support\Facades\Storage::disk('public')->delete($media->file_path_original);
        $media->delete();

        if ($post->media()->count() === 0) {
            $post->delete();
        }

        $this->allMedia = array_values(array_filter($this->allMedia, fn($m) => $m['id'] !== (int)$mediaId));
        $this->dispatch('media-deleted');
    }

    public function render()
    {
        // Reuse the public view but it works because it uses standard variables
        return view('livewire.public-post-preview')
            ->layout('layouts.app');
    }
}
