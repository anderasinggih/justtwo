<?php

namespace App\Livewire;

use App\Models\Relationship;
use App\Models\RelationshipMember;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class InvitePartner extends Component
{
    public $invite_code;
    public $relationship;

    public function mount($code)
    {
        $this->invite_code = $code;
        $this->relationship = Relationship::where('invite_code', $code)->first();
    }

    public function acceptInvite()
    {
        if (!$this->relationship) {
            session()->flash('error', 'invalid invite code.');
            return;
        }

        if ($this->relationship->members()->count() >= 2) {
            session()->flash('error', 'this space is already full.');
            return;
        }

        RelationshipMember::create([
            'relationship_id' => $this->relationship->id,
            'user_id' => Auth::id(),
            'joined_at' => now(),
        ]);

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.invite-partner')->layout('layouts.guest');
    }
}
