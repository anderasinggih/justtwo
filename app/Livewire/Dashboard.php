<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\PostMedia;
use App\Models\WishlistItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $newWishlistTitle = '';

    public function addToWishlist()
    {
        $this->validate([
            'newWishlistTitle' => 'required|string|max:255',
        ]);

        Auth::user()->relationship->wishlistItems()->create([
            'user_id' => Auth::id(),
            'title' => $this->newWishlistTitle,
        ]);

        $this->newWishlistTitle = '';
        $this->dispatch('wishlistUpdated');
    }

    public function toggleWishlist($id)
    {
        $item = Auth::user()->relationship->wishlistItems()->find($id);
        if ($item) {
            $item->update([
                'is_completed' => !$item->is_completed,
                'completed_at' => !$item->is_completed ? now() : null,
            ]);
        }
    }

    public function deleteWishlist($id)
    {
        $item = Auth::user()->relationship->wishlistItems()->find($id);
        if ($item) {
            $item->delete();
        }
    }

    public function render()
    {
        $relationship = Auth::user()->relationship;
        $partner = $relationship->users()->where('users.id', '!=', Auth::id())->first();
        
        $anniversaryDate = $relationship->anniversary_date ?? now();
        $diff = now()->diff($anniversaryDate);
        
        $togetherStats = [
            'total_days' => (int) abs(now()->diffInDays($anniversaryDate)),
            'timestamp' => $anniversaryDate->timestamp * 1000, // milliseconds for JS
            'anniversary_formatted' => $anniversaryDate->format('d F Y'),
        ];

        // Next Milestone Progress
        $nextMilestone = $relationship->milestones()
            ->where('event_date', '>', now())
            ->orderBy('event_date')
            ->first();

        $milestoneProgress = 0;
        $daysRemainingFormatted = '';
        
        if ($nextMilestone) {
            $startDate = $relationship->anniversary_date ?? $nextMilestone->created_at;
            $totalDays = (int) abs($startDate->diffInDays($nextMilestone->event_date));
            $daysPassed = (int) abs($startDate->diffInDays(now()));
            $milestoneProgress = $totalDays > 0 ? min(100, max(0, ($daysPassed / $totalDays) * 100)) : 100;
            
            $daysRemainingFormatted = (int) abs(now()->diffInDays($nextMilestone->event_date)) . ' days again';
        }

        $wishlistItems = $relationship->wishlistItems()
            ->orderBy('is_completed')
            ->latest()
            ->get();

        return view('livewire.dashboard', [
            'relationship' => $relationship,
            'partner' => $partner,
            'togetherStats' => $togetherStats,
            'nextMilestone' => $nextMilestone,
            'milestoneProgress' => $milestoneProgress,
            'daysRemainingFormatted' => $daysRemainingFormatted,
            'wishlistItems' => $wishlistItems,
            'stats' => [
                'total_memories' => $relationship->posts()->count(),
                'total_photos' => PostMedia::whereHas('post', function($q) use ($relationship) {
                    $q->where('relationship_id', $relationship->id);
                })->count(),
            ]
        ]);
    }
}
