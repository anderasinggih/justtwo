<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PrivacySettings extends Component
{
    public $is_public;
    public $allow_comments;

    public function mount()
    {
        $relationship = Auth::user()->relationship;
        $this->is_public = $relationship->is_public;
        $this->allow_comments = $relationship->allow_comments;
    }

    public function updatePrivacy()
    {
        Auth::user()->relationship->update([
            'is_public' => $this->is_public,
            'allow_comments' => $this->allow_comments,
        ]);

        session()->flash('success', 'privacy settings updated.');
    }

    public function render()
    {
        return view('livewire.privacy-settings')
            ->layout('layouts.app');
    }
}
