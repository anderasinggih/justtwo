<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\PostMedia;
use App\Models\RelationshipMember;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Stats extends Component
{
    public function render()
    {
        $relationship = Auth::user()->relationship;
        $users = $relationship->users;

        // Content Breakdown
        $typeStats = $relationship->posts()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type');

        // User Activity
        $userActivity = [];
        foreach ($users as $user) {
            $userActivity[$user->name] = [
                'posts' => $relationship->posts()->where('user_id', $user->id)->count(),
                'media' => PostMedia::whereHas('post', function($q) use ($user, $relationship) {
                    $q->where('user_id', $user->id)->where('relationship_id', $relationship->id);
                })->count(),
            ];
        }

        // Monthly Trend
        $monthlyTrend = $relationship->posts()
            ->selectRaw("DATE_FORMAT(published_at, '%Y-%m') as month, count(*) as count")
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse();

        // Mood Distribution
        $moodStats = $relationship->posts()
            ->whereNotNull('mood')
            ->selectRaw('mood, count(*) as count')
            ->groupBy('mood')
            ->orderBy('count', 'desc')
            ->get();

        return view('livewire.stats', [
            'typeStats' => $typeStats,
            'userActivity' => $userActivity,
            'monthlyTrend' => $monthlyTrend,
            'moodStats' => $moodStats,
            'totalMemories' => $relationship->posts()->count(),
            'totalPhotos' => PostMedia::whereHas('post', function($q) use ($relationship) {
                $q->where('relationship_id', $relationship->id);
            })->count(),
            'daysTogether' => $relationship->days_together,
        ])->layout('layouts.app');
    }
}
