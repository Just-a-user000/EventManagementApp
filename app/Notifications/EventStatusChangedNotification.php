<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EventStatusChangedNotification extends Notification
{
    use Queueable;

    protected $event;
    protected $oldStatus;
    protected $newStatus;

    public function __construct(Event $event, string $oldStatus, string $newStatus)
    {
        $this->event = $event;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => "Lo stato dell'evento '{$this->event->title}' è cambiato",
            'type' => 'status_change'
        ];
    }
}
