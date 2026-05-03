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
    public $savingAmounts = []; // To store inputs for each saving goal
    
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
