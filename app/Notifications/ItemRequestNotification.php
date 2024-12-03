<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ItemRequestNotification extends Notification
{
    protected $items;
    protected $requester;

    public function __construct($items, $requester)
    {
        $this->items = $items;
        $this->requester = $requester;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'New item request from ' . $this->requester->name,
            'items' => $this->items,
            'requester_id' => $this->requester->id,
        ];
    }
}