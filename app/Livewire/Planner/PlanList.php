<?php

namespace App\Livewire\Planner;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PlanList extends Component
{
    public function render()
    {
        $plans = Auth::user()->relationship->plans()
            ->with(['expenses'])
            ->latest()
            ->get();

        return view('livewire.planner.plan-list', [
            'plans' => $plans,
        ])->layout('layouts.app');
    }
}
