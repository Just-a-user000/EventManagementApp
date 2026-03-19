<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserEventController extends Controller
{
    public function myEvents()
    {
        $user = auth()->user();
        $events = $user->registeredEvents()
            ->with('creator')
            ->withCount('participants')
            ->withPivot('registered_at')
            ->orderBy('event_date', 'asc')
            ->get();

        return view('dashboard.my-events', compact('events'));
    }

    public function register(Event $event)
    {
        $user = auth()->user();
        
        if (!$event->isRegistrationOpen()) {
            return back()->with('error', 'Le iscrizioni per questo evento sono chiuse.');
        }

        if ($user->isRegisteredFor($event)) {
            return back()->with('error', 'Sei già iscritto a questo evento.');
        }

        $user->registeredEvents()->attach($event->id, [
            'registered_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Iscrizione completata con successo!');
    }

    public function unregister(Event $event)
    {
        $user = auth()->user();

        if (!$user->isRegisteredFor($event)) {
            return back()->with('error', 'Non sei iscritto a questo evento.');
        }

        if (!$event->canUnregister($user)) {
            return back()->with('error', 'Non è più possibile disiscriversi da questo evento (scadenza: 24 ore prima dell\'evento).');
        }

        $user->registeredEvents()->detach($event->id);

        return back()->with('success', 'Disiscrizione completata con successo!');
    }
}
