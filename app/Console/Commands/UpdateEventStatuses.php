<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Notifications\EventStatusChangedNotification;
use App\Services\WebSocketService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateEventStatuses extends Command
{
    protected $signature = 'events:update-statuses';
    protected $description = 'Automatically update event statuses based on dates';

    protected $webSocketService;

    public function __construct(WebSocketService $webSocketService)
    {
        parent::__construct();
        $this->webSocketService = $webSocketService;
    }

    public function handle()
    {
        $now = Carbon::now();
        $updatedCount = 0;
        
        $completedEvents = Event::where('status', 'published')
            ->where('event_date', '<', $now->toDateString())
            ->get();

        foreach ($completedEvents as $event) {
            $eventDateTime = Carbon::parse($event->event_date->format('Y-m-d') . ' ' . $event->event_time);
            
            if ($eventDateTime->addHours(2)->isPast()) {
                $event->update(['status' => Event::STATUS_COMPLETED]);
                
                $userIds = $event->participants->pluck('id')->toArray();
                
                if (!empty($userIds)) {
                    foreach ($event->participants as $user) {
                        $user->notify(new EventStatusChangedNotification($event, 'completed'));
                    }
                    
                    $this->webSocketService->sendNotificationToMultiple($userIds, [
                        'title' => 'Evento Completato',
                        'message' => "L'evento '{$event->title}' è stato completato. Grazie per la partecipazione!",
                        'event_id' => $event->id,
                        'type' => 'status_change'
                    ]);
                }
                
                $updatedCount++;
                $this->info("Marked event as completed: {$event->title}");
            }
        }

        $this->info("Updated {$updatedCount} event(s) to completed status");
        return 0;
    }
}
