<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RegistrationDeadlineNotification extends Notification
{
    use Queueable;

    protected $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
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
            'deadline' => $this->event->registration_deadline->format('d/m/Y H:i'),
            'message' => "Ultime ore per iscriversi a '{$this->event->title}'",
            'type' => 'deadline'
        ];
    }
}
