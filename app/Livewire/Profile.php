<?php
 
namespace App\Livewire;
 
use App\Models\PostMedia;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
 
class Profile extends Component
{
    public function setTheme($theme)
    {
        $relationship = Auth::user()->relationship;
        $relationship->update(['theme' => $theme]);
        $this->dispatch('theme-updated', theme: $theme);
    }

    public function togglePublic()
    {
        $relationship = Auth::user()->relationship;
        $relationship->update(['is_public' => !$relationship->is_public]);
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    }
 
    public function render()
    {
        $relationship = Auth::user()->relationship;
        $users = $relationship->users;
        $partner = $users->where('id', '!=', Auth::id())->first();
        
        $stats = [
            'days' => (int) abs(now()->diffInDays($relationship->anniversary_date ?? now())),
            'memories' => $relationship->posts()->count(),
            'photos' => PostMedia::whereHas('post', function($q) use ($relationship) {
                $q->where('relationship_id', $relationship->id);
            })->count(),
            'savings' => $relationship->savings()->sum('current_amount'),
        ];

        $milestones = $relationship->milestones()
            ->orderBy('event_date', 'desc')
            ->take(5)
            ->get();
 
        return view('livewire.profile', [
            'relationship' => $relationship,
            'user' => Auth::user(),
            'partner' => $partner,
            'stats' => $stats,
            'milestones' => $milestones,
            'themes' => [
                ['id' => 'light', 'color' => '#f43f5e', 'label' => 'default'],
                ['id' => 'dark', 'color' => '#111827', 'label' => 'night'],
                ['id' => 'rose', 'color' => '#e11d48', 'label' => 'rose'],
                ['id' => 'midnight', 'color' => '#38bdf8', 'label' => 'midnight'],
                ['id' => 'sky', 'color' => '#0ea5e9', 'label' => 'sky'],
                ['id' => 'mint', 'color' => '#22c55e', 'label' => 'mint'],
                ['id' => 'lavender', 'color' => '#8b5cf6', 'label' => 'lavender'],
                ['id' => 'pink', 'color' => '#fecdd3', 'label' => 'sakura'],
            ]
        ])->layout('layouts.app');
    }
}
