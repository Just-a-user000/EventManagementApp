<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EventReminderNotification extends Notification
{
    use Queueable;

    protected $event;
    protected $hours;

    public function __construct(Event $event, int $hours)
    {
        $this->event = $event;
        $this->hours = $hours;
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
            'hours' => $this->hours,
            'message' => "L'evento '{$this->event->title}' inizierà tra {$this->hours} ore",
            'type' => 'reminder'
        ];
    }
}
