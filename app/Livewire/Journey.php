<?php

namespace App\Livewire;

use App\Models\Milestone;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Journey extends Component
{
    public function render()
    {
        $relationship = Auth::user()->relationship;

        // Fetch milestones and posts
        $milestones = $relationship->milestones()->get()->map(function($m) {
            return [
                'date' => $m->event_date,
                'type' => 'milestone',
                'title' => $m->title,
                'content' => $m->description,
                'icon' => 'star',
                'color' => 'brand',
            ];
        });

        $posts = $relationship->posts()
            ->where('is_archived', false)
            ->where('is_secret', false)
            ->with('media')->get()->map(function($p) {
            return [
                'date' => $p->published_at ?? $p->created_at,
                'type' => 'post',
                'title' => $p->title ?? 'a memory',
                'content' => $p->content,
                'image' => $p->media->first()?->file_path,
                'icon' => 'heart',
                'color' => 'rose',
            ];
        });

        // Combine and sort
        $journey = $milestones->concat($posts)->sortByDesc('date');

        return view('livewire.journey', [
            'journey' => $journey,
        ])->layout('layouts.app');
    }
}
