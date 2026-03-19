<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\WebSocketService;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::withCount('participants');

        if (!auth()->check() || !auth()->user()?->isAdmin()) {
            $query->published();
        }

        if ($request->has('type') && $request->type != '') {
            $query->ofType($request->type);
        }

        if ($request->has('search') && strlen($request->search) >= 1) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->has('price_filter')) {
            if ($request->price_filter === 'free') {
                $query->where(function($q) {
                    $q->whereNull('cost')->orWhere('cost', 0);
                });
            } elseif ($request->price_filter === 'paid') {
                $query->where('cost', '>', 0);
            }
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $query->where('event_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->where('event_date', '<=', $request->date_to);
        }

        if ($request->has('available_only') && $request->available_only == '1') {
            $query->where(function($q) {
                $q->whereNull('max_participants')
                  ->orWhereRaw('(SELECT COUNT(*) FROM event_user WHERE event_id = events.id) < max_participants');
            });
        }

        if ($request->has('open_registration') && $request->open_registration == '1') {
            $query->where('registration_deadline', '>', now());
        }

        $events = $query->upcoming()->paginate(12)->withQueryString();

        return view('events.index', compact('events'));
    }

    public function create()
    {
        $this->authorize('create', Event::class);
        return view('events.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Event::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'max_participants' => 'nullable|integer|min:1',
            'cost' => 'nullable|numeric|min:0',
            'event_date' => 'required|date|after_or_equal:today',
            'event_time' => 'required',
            'registration_deadline' => 'required|date|before:event_date',
            'event_type' => 'required|in:cultural,recreational,educational,sports,other',
            'status' => 'required|in:draft,published',
            'latitude' => 'nullable|numeric|min:-90|max:90',
            'longitude' => 'nullable|numeric|min:-180|max:180',
        ], [
            'title.required' => 'Il titolo è obbligatorio.',
            'title.max' => 'Il titolo non può superare i 255 caratteri.',
            'description.required' => 'La descrizione è obbligatoria.',
            'location.required' => 'Il luogo è obbligatorio.',
            'location.max' => 'Il luogo non può superare i 255 caratteri.',
            'max_participants.integer' => 'Il numero massimo di partecipanti deve essere un numero intero.',
            'max_participants.min' => 'Il numero massimo di partecipanti deve essere almeno 1.',
            'cost.numeric' => 'Il costo deve essere un valore numerico.',
            'cost.min' => 'Il costo non può essere negativo.',
            'event_date.required' => 'La data dell\'evento è obbligatoria.',
            'event_date.date' => 'La data dell\'evento non è valida.',
            'event_date.after_or_equal' => 'La data dell\'evento deve essere oggi o una data futura.',
            'event_time.required' => 'L\'orario dell\'evento è obbligatorio.',
            'registration_deadline.required' => 'La scadenza delle iscrizioni è obbligatoria.',
            'registration_deadline.date' => 'La scadenza delle iscrizioni non è una data valida.',
            'registration_deadline.before' => 'La scadenza delle iscrizioni deve essere precedente alla data dell\'evento.',
            'event_type.required' => 'La tipologia dell\'evento è obbligatoria.',
            'event_type.in' => 'La tipologia selezionata non è valida.',
            'status.required' => 'Lo stato è obbligatorio.',
            'status.in' => 'Lo stato selezionato non è valido.',
            'latitude.numeric' => 'La latitudine deve essere un valore numerico.',
            'latitude.min' => 'La latitudine deve essere compresa tra -90 e 90.',
            'latitude.max' => 'La latitudine deve essere compresa tra -90 e 90.',
            'longitude.numeric' => 'La longitudine deve essere un valore numerico.',
            'longitude.min' => 'La longitudine deve essere compresa tra -180 e 180.',
            'longitude.max' => 'La longitudine deve essere compresa tra -180 e 180.',
        ]);

        $validated['created_by'] = auth()->id();

        $event = Event::create($validated);

        // Invia notifiche WebSocket se l'evento è pubblicato
        if ($event->status === 'published') {
            $wsService = app(WebSocketService::class);
            $users = User::where('id', '!=', auth()->id())->get();
            
            foreach ($users as $user) {
                // Notifica database usando insert diretto con UUID
                DB::table('notifications')->insert([
                    'id' => \Illuminate\Support\Str::uuid()->toString(),
                    'type' => 'App\Notifications\EventCreated',
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => $user->id,
                    'data' => json_encode([
                        'event_id' => $event->id,
                        'event_title' => $event->title,
                        'message' => "Nuovo evento disponibile: {$event->title}"
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // Notifica WebSocket real-time
                $wsService->sendNotification($user->id, [
                    'title' => 'Nuovo Evento',
                    'message' => "È stato pubblicato un nuovo evento: {$event->title}",
                    'type' => 'info'
                ]);
            }
        }

        return redirect()->route('admin.dashboard')
            ->with('success', 'Evento creato con successo!');
    }

    public function show(Event $event)
    {
        if ($event->isDraft() && (!auth()->check() || !auth()->user()?->isAdmin())) {
            abort(403, 'Non hai i permessi per visualizzare questo evento.');
        }

        $event->loadCount('participants');
        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'max_participants' => 'nullable|integer|min:1',
            'cost' => 'nullable|numeric|min:0',
            'event_date' => 'required|date|after_or_equal:today',
            'event_time' => 'required',
            'registration_deadline' => 'required|date|before:event_date',
            'event_type' => 'required|in:cultural,recreational,educational,sports,other',
            'status' => 'required|in:draft,published,completed,cancelled',
            'latitude' => 'nullable|numeric|min:-90|max:90',
            'longitude' => 'nullable|numeric|min:-180|max:180',
        ], [
            'title.required' => 'Il titolo è obbligatorio.',
            'title.max' => 'Il titolo non può superare i 255 caratteri.',
            'description.required' => 'La descrizione è obbligatoria.',
            'location.required' => 'Il luogo è obbligatorio.',
            'location.max' => 'Il luogo non può superare i 255 caratteri.',
            'max_participants.integer' => 'Il numero massimo di partecipanti deve essere un numero intero.',
            'max_participants.min' => 'Il numero massimo di partecipanti deve essere almeno 1.',
            'cost.numeric' => 'Il costo deve essere un valore numerico.',
            'cost.min' => 'Il costo non può essere negativo.',
            'event_date.required' => 'La data dell\'evento è obbligatoria.',
            'event_date.date' => 'La data dell\'evento non è valida.',
            'event_date.after_or_equal' => 'La data dell\'evento deve essere oggi o una data futura.',
            'event_time.required' => 'L\'orario dell\'evento è obbligatorio.',
            'registration_deadline.required' => 'La scadenza delle iscrizioni è obbligatoria.',
            'registration_deadline.date' => 'La scadenza delle iscrizioni non è una data valida.',
            'registration_deadline.before' => 'La scadenza delle iscrizioni deve essere precedente alla data dell\'evento.',
            'event_type.required' => 'La tipologia dell\'evento è obbligatoria.',
            'event_type.in' => 'La tipologia selezionata non è valida.',
            'status.required' => 'Lo stato è obbligatorio.',
            'status.in' => 'Lo stato selezionato non è valido.',
            'latitude.numeric' => 'La latitudine deve essere un valore numerico.',
            'latitude.min' => 'La latitudine deve essere compresa tra -90 e 90.',
            'latitude.max' => 'La latitudine deve essere compresa tra -90 e 90.',
            'longitude.numeric' => 'La longitudine deve essere un valore numerico.',
            'longitude.min' => 'La longitudine deve essere compresa tra -180 e 180.',
            'longitude.max' => 'La longitudine deve essere compresa tra -180 e 180.',
        ]);

        $event->update($validated);

        return redirect()->route('events.show', $event)
            ->with('success', 'Evento aggiornato con successo!');
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        $event->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Evento eliminato con successo!');
    }

    public function adminDashboard()
    {
        $this->authorize('viewAny', Event::class);
        
        $stats = [
            'total' => Event::count(),
            'published' => Event::where('status', 'published')->count(),
            'draft' => Event::where('status', 'draft')->count(),
            'completed' => Event::where('status', 'completed')->count(),
            'cancelled' => Event::where('status', 'cancelled')->count(),
            'total_participants' => DB::table('event_user')->count(),
            'upcoming_events' => Event::where('status', 'published')->where('event_date', '>=', now())->count(),
        ];

        $typeStats = Event::selectRaw('event_type, COUNT(*) as count, AVG((SELECT COUNT(*) FROM event_user WHERE event_user.event_id = events.id)) as avg_participants')
            ->groupBy('event_type')
            ->get();

        $recentEvents = Event::with('creator')->withCount('participants')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $events = Event::with('creator')->withCount('participants')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('events.admin-dashboard', compact('events', 'stats', 'typeStats', 'recentEvents'));
    }


    public function exportParticipants(Event $event)
    {
        $this->authorize('update', $event);

        $participants = $event->participants()->withPivot('registered_at')->get();

        $filename = 'participants_' . \Str::slug($event->title) . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($participants) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Nome', 'Email', 'Data Iscrizione']);

            foreach ($participants as $participant) {
                fputcsv($file, [
                    $participant->id,
                    $participant->name,
                    $participant->email,
                    \Carbon\Carbon::parse($participant->pivot->registered_at)->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportEvents()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $events = Event::with('creator')->withCount('participants')->get();

        $filename = 'events_export_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($events) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Titolo', 'Tipologia', 'Data', 'Orario', 'Luogo', 'Costo', 'Max Partecipanti', 'Iscritti', 'Stato', 'Creato da']);

            foreach ($events as $event) {
                fputcsv($file, [
                    $event->id,
                    $event->title,
                    $event->event_type,
                    $event->event_date->format('d/m/Y'),
                    $event->event_time,
                    $event->location,
                    $event->cost ?? 'Gratuito',
                    $event->max_participants ?? 'Illimitati',
                    $event->participants_count,
                    $event->status,
                    $event->creator->name
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
