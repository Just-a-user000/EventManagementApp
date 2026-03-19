<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Notifications\EventReminderNotification;
use App\Services\WebSocketService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendEventReminders extends Command
{
    protected $signature = 'events:send-reminders';
    protected $description = 'Send event reminders to registered users';

    protected $webSocketService;

    public function __construct(WebSocketService $webSocketService)
    {
        parent::__construct();
        $this->webSocketService = $webSocketService;
    }

    public function handle()
    {
        $now = Carbon::now();
        
        $reminders = [
            ['hours' => 24, 'start' => $now->copy()->addHours(23)->addMinutes(50), 'end' => $now->copy()->addHours(24)->addMinutes(10)],
            ['hours' => 1, 'start' => $now->copy()->addMinutes(50), 'end' => $now->copy()->addHours(1)->addMinutes(10)]
        ];

        foreach ($reminders as $reminder) {
            $events = Event::where('status', 'published')
                ->whereBetween('event_date', [$reminder['start']->toDateString(), $reminder['end']->toDateString()])
                ->with('participants')
                ->get();

            foreach ($events as $event) {
                $eventDateTime = Carbon::parse($event->event_date->format('Y-m-d') . ' ' . $event->event_time);
                
                if ($eventDateTime->between($reminder['start'], $reminder['end'])) {
                    $userIds = [];
                    
                    foreach ($event->participants as $user) {
                        $user->notify(new EventReminderNotification($event, $reminder['hours']));
                        $userIds[] = $user->id;
                    }
                    
                    if (!empty($userIds)) {
                        $this->webSocketService->sendNotificationToMultiple($userIds, [
                            'title' => 'Promemoria Evento',
                            'message' => "L'evento '{$event->title}' inizierà tra {$reminder['hours']} " . ($reminder['hours'] == 1 ? 'ora' : 'ore') . "!",
                            'event_id' => $event->id,
                            'type' => 'reminder'
                        ]);
                    }
                    
                    $this->info("Sent {$reminder['hours']}h reminder for event: {$event->title} to {$event->participants->count()} users");
                }
            }
        }

        $this->info('Event reminders sent successfully!');
        return 0;
    }
}
