<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkshopTransferRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'New workshop transfer request for items from your shop',
            'items' => $this->data['items'],
            'reason' => $this->data['reason'],
            'requested_by' => $this->data['requested_by'],
            'shop_name' => $notifiable->shop_name
        ];
    }
}
