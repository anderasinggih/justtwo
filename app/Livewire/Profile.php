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
            ->with(['media', 'user'])
            ->orderBy('published_at', 'desc')
            ->get();

        return view('livewire.profile', [
            'relationship' => $relationship,
            'user' => Auth::user(),
            'partner' => $partner,
            'postsCount' => $postsCount,
            'daysTogether' => $daysTogether,
            'milestonesCount' => $milestonesCount,
            'posts' => $posts,
        ])->layout('layouts.app');
    }
}
