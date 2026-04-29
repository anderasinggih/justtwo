<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\PostMedia;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Profile extends Component
{
    public function render()
    {
        $relationship = Auth::user()->relationship;
        $users = $relationship->users;
        $partner = $users->where('id', '!=', Auth::id())->first();
        
        $postsCount = $relationship->posts()->count();
        $daysTogether = $relationship->days_together;
        $milestonesCount = $relationship->milestones()->count();
        
        $posts = $relationship->posts()
            ->where('is_archived', false)
            ->where('is_secret', false)
            ->with(['media', 'user'])
            ->orderBy('published_at', 'desc')
            ->get();

        $bookmarkedPosts = Auth::user()->bookmarks()
            ->with(['post.media', 'post.user'])
            ->get()
            ->pluck('post');

        return view('livewire.profile', [
            'relationship' => $relationship,
            'user' => Auth::user(),
            'partner' => $partner,
            'postsCount' => $postsCount,
            'daysTogether' => $daysTogether,
            'milestonesCount' => $milestonesCount,
            'posts' => $posts,
            'bookmarkedPosts' => $bookmarkedPosts,
        ])->layout('layouts.app');
    }
}
