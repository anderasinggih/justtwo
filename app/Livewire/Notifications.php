<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Notifications extends Component
{
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        $notifications = Auth::user()->notifications()->latest()->limit(10)->get();

        return view('livewire.notifications', [
            'notifications' => $notifications,
            'unreadCount' => Auth::user()->unreadNotifications()->count(),
        ]);
    }
}
