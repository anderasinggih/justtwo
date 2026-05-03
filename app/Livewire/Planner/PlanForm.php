<?php
 
namespace App\Livewire\Planner;
 
use App\Models\Plan;
use App\Models\Saving;
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
    public $saving_id;
    public $priority = 'medium';
    public $category = '';
    public $status = 'draft';
 
    public function mount($plan = null)
    {
        if ($plan) {
            $planModel = Plan::findOrFail($plan);
            $this->planId = $planModel->id;
            $this->title = $planModel->title;
            $this->target_date = $planModel->target_date?->format('Y-m-d');
            $this->total_budget = $planModel->total_budget;
            $this->description = $planModel->description;
            $this->saving_id = $planModel->saving_id;
            $this->priority = $planModel->priority ?? 'medium';
            $this->category = $planModel->category;
            $this->status = $planModel->status ?? 'draft';
        }
    }

    public function updatedSavingId($value)
    {
        if ($value) {
            $saving = Saving::find($value);
            if ($saving && $saving->target_amount > 0) {
                $this->total_budget = $saving->target_amount;
            }
        }
    }

    public function rules()
    {
        return [
            'title' => 'required|string|min:3|max:255',
            'target_date' => 'nullable|date|after_or_equal:today',
            'total_budget' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'saving_id' => 'nullable|exists:savings,id',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:draft,ongoing,completed,cancelled',
            'category' => 'nullable|string|max:50',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
 
    public function save()
    {
        $this->validate();
 
        $data = [
            'relationship_id' => Auth::user()->relationship->id,
            'saving_id' => $this->saving_id ?: null,
            'title' => $this->title,
            'target_date' => $this->target_date ?: null,
            'total_budget' => $this->total_budget ?: 0,
            'description' => $this->description,
            'priority' => $this->priority,
            'category' => $this->category,
            'status' => $this->status,
        ];
 
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
        return view('livewire.planner.plan-form', [
            'savings' => Auth::user()->relationship->savings
        ])->layout('layouts.app');
    }
}
