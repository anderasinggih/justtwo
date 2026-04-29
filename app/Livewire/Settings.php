<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

use Livewire\WithFileUploads;

class Settings extends Component
{
    use WithFileUploads;

    public $relationship_name;
    public $anniversary_date;
    public $user_name;
    public $theme;
    public $profile_photo;

    public function mount()
    {
        $relationship = Auth::user()->relationship;
        $this->relationship_name = $relationship->name;
        $this->anniversary_date = $relationship->anniversary_date?->format('Y-m-d');
        $this->user_name = Auth::user()->name;
        $this->theme = $relationship->theme ?? 'light';
    }

    public function updatedTheme($value)
    {
        $this->validateOnly('theme', [
            'theme' => 'required|in:light,dark,rose,midnight',
        ]);

        Auth::user()->relationship->update([
            'theme' => $value,
        ]);

        $this->dispatch('theme-updated', theme: $value);
    }

    public function updateRelationship()
    {
        $this->validate([
            'relationship_name' => 'required|string|max:255',
            'anniversary_date' => 'required|date',
            'theme' => 'required|in:light,dark,rose,midnight',
        ]);


        Auth::user()->relationship->update([
            'name' => $this->relationship_name,
            'anniversary_date' => $this->anniversary_date,
            'theme' => $this->theme,
        ]);

        $this->dispatch('themeUpdated', theme: $this->theme);
        session()->flash('success', 'shared space updated.');
    }

    public function updateProfile()
    {
        $this->validate([
            'user_name' => 'required|string|max:255',
            'profile_photo' => 'nullable|image|max:10240', // 10MB Max
        ]);

        $data = [
            'name' => $this->user_name,
        ];

        if ($this->profile_photo) {
            $data['profile_photo_path'] = $this->profile_photo->store('profile-photos', 'public');
        }

        Auth::user()->update($data);

        session()->flash('profile_success', 'profile updated.');
    }

    public function exportMemories()
    {
        $relationship = Auth::user()->relationship;
        $media = \App\Models\PostMedia::whereHas('post', function($q) use ($relationship) {
            $q->where('relationship_id', $relationship->id);
        })->whereNotNull('file_path_original')->get();

        if ($media->isEmpty()) {
            session()->flash('error', 'no memories to export.');
            return;
        }

        $zipFileName = 'memories-' . now()->format('Y-m-d') . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);

        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            foreach ($media as $item) {
                $filePath = storage_path('app/public/' . $item->file_path_original);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $item->original_file_name ?? basename($filePath));
                }
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function render()
    {
        return view('livewire.settings')->layout('layouts.app');
    }
}

