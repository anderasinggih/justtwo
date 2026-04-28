<?php

namespace App\Livewire;

use App\Models\Relationship;
use App\Models\RelationshipMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Onboarding extends Component
{
    public $name;
    public $invite_code;

    protected $rules = [
        'name' => 'required|string|min:3|max:255',
    ];

    public function createSpace()
    {
        $this->validate();

        $relationship = Relationship::create([
            'name' => $this->name,
            'creator_id' => Auth::id(),
            'anniversary_date' => now(), // Default to today, can be changed later
        ]);


        RelationshipMember::create([
            'relationship_id' => $relationship->id,
            'user_id' => Auth::id(),
            'joined_at' => now(),
        ]);

        return redirect()->route('dashboard');
    }

    public function joinSpace()
    {
        $this->validate([
            'invite_code' => 'required|string|exists:relationships,invite_code',
        ]);

        $relationship = Relationship::where('invite_code', $this->invite_code)->first();

        // Check if relationship already has 2 members
        if ($relationship->members()->count() >= 2) {
            $this->addError('invite_code', 'This shared space is already full.');
            return;
        }

        RelationshipMember::create([
            'relationship_id' => $relationship->id,
            'user_id' => Auth::id(),
            'joined_at' => now(),
        ]);

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.onboarding');
    }
}
