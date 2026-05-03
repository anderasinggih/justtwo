<?php

namespace App\Livewire\Planner;

use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class PlanForm extends Component
{
    use WithFileUploads;

    public $planId;
    public $title = '';
    public $target_date = '';
    public $total_budget = '';
    public $description = '';
    public $cover_image;

    public function mount($plan = null)
    {
        if ($plan) {
            $planModel = Plan::findOrFail($plan);
            $this->planId = $planModel->id;
            $this->title = $planModel->title;
            $this->target_date = $planModel->target_date?->format('Y-m-d');
            $this->total_budget = $planModel->total_budget;
            $this->description = $planModel->description;
        }
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'target_date' => 'nullable|date',
            'total_budget' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|max:1024',
        ]);

        $data = [
            'relationship_id' => Auth::user()->relationship->id,
            'title' => $this->title,
            'target_date' => $this->target_date ?: null,
            'total_budget' => $this->total_budget ?: 0,
            'description' => $this->description,
        ];

        if ($this->cover_image) {
            $data['cover_image'] = $this->cover_image->store('plans', 'public');
        }

        if ($this->planId) {
            $plan = Plan::findOrFail($this->planId);
            $plan->update($data);
        } else {
            $plan = Plan::create($data);
        }

        return redirect()->route('planner.detail', $plan->id);
    }

    public function render()
    {
        return view('livewire.planner.plan-form')->layout('layouts.app');
    }
}
