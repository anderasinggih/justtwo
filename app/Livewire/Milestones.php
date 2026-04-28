<?php

namespace App\Livewire;

use App\Models\Milestone;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Milestones extends Component
{
    public $title;
    public $event_date;
    public $description;
    public $category = 'other';

    public function saveMilestone()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'category' => 'required|string',
        ]);

        Milestone::create([
            'relationship_id' => Auth::user()->relationship->id,
            'title' => $this->title,
            'event_date' => $this->event_date,
            'description' => $this->description,
            'category' => $this->category,
        ]);

        $this->reset(['title', 'event_date', 'description', 'category']);
    }

    public function render()
    {
        $milestones = Auth::user()->relationship->milestones()
            ->orderBy('event_date', 'desc')
            ->get();

        return view('livewire.milestones', [
            'milestones' => $milestones,
            'categories' => [
                'first' => '✨',
                'travel' => '✈️',
                'date' => '🍽️',
                'anniversary' => '💍',
                'other' => '🍃',
            ]
        ])->layout('layouts.app');
    }
}

