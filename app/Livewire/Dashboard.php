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
    
    public function mount()
    {
        // Pre-initialize array with empty strings for each saving goal
        $savings = Auth::user()->relationship->savings;
        foreach ($savings as $saving) {
            $this->savingAmounts[$saving->id] = '';
        }
    }

    public function addSaving($savingId)
    {
        $saving = Auth::user()->relationship->savings()->findOrFail($savingId);
        
        // Prevent adding if already reached target
        if ($saving->current_amount >= $saving->target_amount) {
            session()->flash('saving-error-' . $savingId, 'Goal reached! 🎉');
            return;
        }

        $amount = (int) ($this->savingAmounts[$savingId] ?? 0);
        if ($amount <= 0) return;

        // Optional: Cap the amount to not exceed target
        $remaining = $saving->target_amount - $saving->current_amount;
        $finalAmount = min($amount, $remaining);
        
        $saving->increment('current_amount', $finalAmount);
        
        $saving->logs()->create([
            'user_id' => Auth::id(),
            'amount' => $finalAmount,
        ]);
        
        $this->savingAmounts[$savingId] = '';
        $this->dispatch('savingUpdated');
        
        session()->flash('saving-success-' . $savingId, 'Saved! 💰');
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
