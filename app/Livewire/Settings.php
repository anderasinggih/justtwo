<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

use Livewire\WithFileUploads;

class Settings extends Component
{
    use WithFileUploads;

    public $user_name;
    public $bio;
    public $profile_photo;

    public function mount()
    {
        $this->user_name = Auth::user()->name;
        $this->bio = Auth::user()->bio;
    }

    public function updateProfile()
    {
        $this->validate([
            'user_name' => 'required|string|max:255',
            'profile_photo' => 'nullable|image|max:10240', // 10MB Max
        ]);

        $data = [
            'name' => $this->user_name,
            'bio' => $this->bio,
        ];

        if ($this->profile_photo) {
            $data['profile_photo_path'] = $this->profile_photo->store('profile-photos', 'public');
        }

        Auth::user()->update($data);

        session()->flash('success', 'profile updated successfully.');
        
        if ($this->profile_photo) {
            return redirect()->route('profile');
        }
    }

    public function render()
    {
        return view('livewire.settings')->layout('layouts.app');
    }
}

