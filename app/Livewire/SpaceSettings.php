<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SpaceSettings extends Component
{
    public $relationship_name;
    public $anniversary_date;

    public function mount()
    {
        $relationship = Auth::user()->relationship;
        $this->relationship_name = $relationship->name;
        $this->anniversary_date = $relationship->anniversary_date?->format('Y-m-d');
    }

    public function updateRelationship()
    {
        $this->validate([
            'relationship_name' => 'required|string|max:255',
            'anniversary_date' => 'required|date',
        ]);

        Auth::user()->relationship->update([
            'name' => $this->relationship_name,
            'anniversary_date' => $this->anniversary_date,
        ]);

        session()->flash('success', 'shared space updated.');
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
        return view('livewire.space-settings')
            ->layout('layouts.app');
    }
}
