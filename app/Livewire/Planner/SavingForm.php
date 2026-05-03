<?php

namespace App\Livewire\Planner;

use App\Models\Saving;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SavingForm extends Component
{
    public $title = '';
    public $target_amount = '';
    public $icon = '💰';
    public $savingId;

    public function mount($saving = null)
    {
        if ($saving) {
            $savingModel = Saving::findOrFail($saving);
            $this->savingId = $savingModel->id;
            $this->title = $savingModel->title;
            $this->target_amount = $savingModel->target_amount;
            $this->icon = $savingModel->icon ?? '💰';
        }
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0',
            'icon' => 'nullable|string|max:10',
        ]);

        $data = [
            'relationship_id' => Auth::user()->relationship->id,
            'title' => $this->title,
            'target_amount' => $this->target_amount,
            'icon' => $this->icon,
        ];

        if ($this->savingId) {
            $saving = Saving::findOrFail($this->savingId);
            $saving->update($data);
        } else {
            Saving::create($data);
        }

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.planner.saving-form')->layout('layouts.app');
    }
}
