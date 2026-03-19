<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Notifications\RegistrationDeadlineNotification;
use App\Services\WebSocketService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckRegistrationDeadlines extends Command
{
    protected $signature = 'events:check-deadlines';
    protected $description = 'Check and notify users about upcoming registration deadlines';

    protected $webSocketService;

    public function __construct(WebSocketService $webSocketService)
    {
        parent::__construct();
        $this->webSocketService = $webSocketService;
    }

    public function handle()
    {
        $now = Carbon::now();
        $tomorrow = $now->copy()->addDay();
        
        $events = Event::where('status', 'published')
            ->whereNotNull('registration_deadline')
            ->whereBetween('registration_deadline', [$now, $tomorrow])
            ->whereDoesntHave('participants', function($query) {
                $query->whereNull('notified_deadline');
            })
            ->with('participants')
            ->get();

        foreach ($events as $event) {
            $hoursRemaining = $now->diffInHours($event->registration_deadline);
            
            if ($hoursRemaining <= 24 && $hoursRemaining > 0) {
                $allUserIds = \App\Models\User::where('role', 'user')
                    ->whereNotIn('id', $event->participants->pluck('id'))
                    ->pluck('id')
                    ->toArray();
                
                if (!empty($allUserIds)) {
                    $this->webSocketService->sendNotificationToMultiple($allUserIds, [
                        'title' => 'Scadenza Iscrizione',
                        'message' => "Ultime {$hoursRemaining} ore per iscriverti a '{$event->title}'!",
                        'event_id' => $event->id,
                        'type' => 'deadline'
                    ]);
                    
                    $this->info("Notified users about deadline for event: {$event->title}");
                }
            }
        }

        $this->info('Registration deadline checks completed!');
        return 0;
    }
}
