<?php
 
namespace App\Livewire;
 
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\Saving;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
 
class Dashboard extends Component
{
    public $savingAmounts = []; 
    
    public function addSaving($savingId)
    {
        $amount = $this->savingAmounts[$savingId] ?? 0;
        if ($amount <= 0) return;
        $saving = Auth::user()->relationship->savings()->findOrFail($savingId);
        $saving->increment('current_amount', $amount);
        $saving->logs()->create([
            'user_id' => Auth::id(),
            'amount' => $amount,
        ]);
        $this->savingAmounts[$savingId] = '';
        $this->dispatch('savingUpdated');
    }
 
    public function render()
    {
        $relationship = Auth::user()->relationship;
        $partners = $relationship->users()->get();
        
        // Anniversary Stats
        $anniversaryDate = $relationship->anniversary_date ?? now();
        $togetherStats = [
            'total_days' => (int) abs(now()->diffInDays($anniversaryDate)),
            'timestamp' => $anniversaryDate->timestamp * 1000,
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

        $upcomingEvents = $relationship->milestones()
            ->where('event_date', '>=', now())
            ->orderBy('event_date')
            ->take(3)
            ->get();

        $savings = $relationship->savings()
            ->with(['logs', 'logs.user'])
            ->latest()
            ->get();

        $upcomingPlans = $relationship->plans()
            ->where('status', 'planning')
            ->latest()
            ->take(3)
            ->get();
 
        return view('livewire.dashboard', [
            'relationship' => $relationship,
            'partners' => $partners,
            'togetherStats' => $togetherStats,
            'nextMilestone' => $nextMilestone,
            'milestoneProgress' => $milestoneProgress,
            'daysRemainingFormatted' => $daysRemainingFormatted,
            'upcomingEvents' => $upcomingEvents,
            'savings' => $savings,
            'upcomingPlans' => $upcomingPlans,
            'stats' => [
                'total_memories' => $relationship->posts()->count(),
                'total_photos' => PostMedia::whereHas('post', function($q) use ($relationship) {
                    $q->where('relationship_id', $relationship->id);
                })->count(),
            ]
        ]);
    }
}
