<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventMapController extends Controller
{
    public function index()
    {
        $events = Event::published()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with('creator')
            ->withCount('participants')
            ->get();

        return view('events.map', compact('events'));
    }

    public function getEventsJson(Request $request)
    {
        $query = Event::published()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->withCount('participants');

        if ($request->has('type') && $request->type != '') {
            $query->ofType($request->type);
        }

        if ($request->has('free') && $request->free == '1') {
            $query->whereNull('cost')->orWhere('cost', 0);
        }

        if ($request->has('available') && $request->available == '1') {
            $query->where(function($q) {
                $q->whereNull('max_participants')
                  ->orWhereRaw('(SELECT COUNT(*) FROM event_user WHERE event_id = events.id) < max_participants');
            });
        }

        $events = $query->get()->map(function($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'description' => \Illuminate\Support\Str::limit($event->description, 100),
                'location' => $event->location,
                'latitude' => (float) $event->latitude,
                'longitude' => (float) $event->longitude,
                'event_date' => $event->event_date->format('d/m/Y'),
                'event_time' => $event->event_time,
                'event_type' => $event->event_type,
                'cost' => $event->cost ? number_format($event->cost, 2) . ' €' : 'Gratuito',
                'participants_count' => $event->participants_count,
                'max_participants' => $event->max_participants,
                'is_full' => $event->isFull(),
                'url' => route('events.show', $event)
            ];
        });

        return response()->json($events);
    }
}
