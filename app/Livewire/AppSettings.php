<?php

namespace App\Livewire;

use App\Livewire\Actions\Logout;
use Livewire\Component;

class AppSettings extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.app-settings')
            ->layout('layouts.app');
    }
}
