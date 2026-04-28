<?php

namespace App\Livewire;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Calendar extends Component
{
    public $month;
    public $year;
    public $selectedDate;

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
        $this->selectedDate = now()->toDateString();
    }

    public function nextMonth()
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1)->addMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function prevMonth()
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1)->subMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
    }

    public function render()
    {
        $relationship = Auth::user()->relationship;
        $startDate = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Get all memories for this month
        $memories = $relationship->posts()
            ->whereBetween('published_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->with(['media', 'user'])
            ->get()
            ->groupBy(function($item) {
                return $item->published_at->format('Y-m-d');
            });

        // Get milestones for this month
        $milestones = $relationship->milestones()
            ->whereBetween('event_date', [$startDate, $endDate])
            ->get()
            ->groupBy(function($item) {
                return $item->event_date->format('Y-m-d');
            });

        // Calendar Grid Logic
        $daysInMonth = $startDate->daysInMonth;
        $firstDayOfWeek = $startDate->dayOfWeek; // 0 (Sun) to 6 (Sat)
        
        $calendar = [];
        
        // Pad beginning
        for ($i = 0; $i < $firstDayOfWeek; $i++) {
            $calendar[] = null;
        }
        
        // Fill days
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateString = Carbon::createFromDate($this->year, $this->month, $day)->toDateString();
            $calendar[] = [
                'day' => $day,
                'date' => $dateString,
                'has_memories' => isset($memories[$dateString]),
                'has_milestones' => isset($milestones[$dateString]),
                'is_today' => $dateString === now()->toDateString(),
            ];
        }

        // Selected date details
        $selectedMemories = $memories[$this->selectedDate] ?? collect();
        $selectedMilestones = $milestones[$this->selectedDate] ?? collect();

        return view('livewire.calendar', [
            'calendar' => $calendar,
            'monthName' => $startDate->format('F'),
            'selectedMemories' => $selectedMemories,
            'selectedMilestones' => $selectedMilestones,
            'selectedDateFormatted' => Carbon::parse($this->selectedDate)->format('M d, Y'),
        ])->layout('layouts.app');
    }
}
