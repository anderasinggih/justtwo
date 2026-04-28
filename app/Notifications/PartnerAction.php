<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PartnerAction extends Notification
{
    use Queueable;

    public $type;
    public $user_name;
    public $message;

    public function __construct($type, $user_name, $message)
    {
        $this->type = $type;
        $this->user_name = $user_name;
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => $this->type,
            'user_name' => $this->user_name,
            'message' => $this->message,
        ];
    }
}
