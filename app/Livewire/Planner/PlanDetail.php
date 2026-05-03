<?php

namespace App\Livewire\Planner;

use App\Models\Plan;
use App\Models\PlanExpense;
use App\Models\PlanItinerary;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PlanDetail extends Component
{
    public Plan $plan;
    
    // Expense Fields
    public $expenseTitle = '';
    public $expenseAmount = '';
    public $expenseCategory = 'general';

    // Itinerary Fields
    public $itineraryDate = '';
    public $itineraryTime = '';
    public $itineraryActivity = '';
    public $itineraryNotes = '';

    public function mount($plan)
    {
        $this->plan = Plan::with(['expenses', 'itineraries'])->findOrFail($plan);
        $this->itineraryDate = $this->plan->target_date?->format('Y-m-d') ?? now()->format('Y-m-d');
    }

    public function addExpense()
    {
        $this->validate([
            'expenseTitle' => 'required|string|max:255',
            'expenseAmount' => 'required|numeric|min:0',
            'expenseCategory' => 'required|string',
        ]);

        $this->plan->expenses()->create([
            'title' => $this->expenseTitle,
            'amount' => $this->expenseAmount,
            'category' => $this->expenseCategory,
        ]);

        $this->expenseTitle = '';
        $this->expenseAmount = '';
        $this->plan->refresh();
    }

    public function deleteExpense($id)
    {
        $this->plan->expenses()->findOrFail($id)->delete();
        $this->plan->refresh();
    }

    public function addItinerary()
    {
        $this->validate([
            'itineraryDate' => 'required|date',
            'itineraryActivity' => 'required|string|max:255',
        ]);

        $this->plan->itineraries()->create([
            'event_date' => $this->itineraryDate,
            'event_time' => $this->itineraryTime ?: null,
            'activity' => $this->itineraryActivity,
            'notes' => $this->itineraryNotes,
        ]);

        $this->itineraryActivity = '';
        $this->itineraryTime = '';
        $this->itineraryNotes = '';
        $this->plan->refresh();
    }

    public function deleteItinerary($id)
    {
        $this->plan->itineraries()->findOrFail($id)->delete();
        $this->plan->refresh();
    }

    public function render()
    {
        return view('livewire.planner.plan-detail')->layout('layouts.app');
    }
}
