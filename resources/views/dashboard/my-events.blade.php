@extends('layouts.app')

@section('title', 'I Miei Eventi')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0"><i class="bi bi-person-circle"></i> I Miei Eventi</h1>
                <div>
                    <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary btn-sm me-2">
                        <i class="bi bi-bell"></i> Notifiche
                        @if(auth()->user()->unreadNotifications()->count() > 0)
                            <span class="badge bg-danger rounded-pill">{{ auth()->user()->unreadNotifications()->count() }}</span>
                        @endif
                    </a>
                    <a href="{{ route('preferences.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-gear"></i> Preferenze
                    </a>
                </div>
            </div>

            @php
                $upcomingEvents = $events->filter(fn($e) => $e->event_date >= now()->toDateString());
                $pastEvents = $events->filter(fn($e) => $e->event_date < now()->toDateString());
            @endphp

            @if($events->isEmpty())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Non sei ancora iscritto a nessun evento.
                    <a href="{{ route('events.index') }}" class="alert-link">Esplora gli eventi disponibili</a>
                </div>
            @else
               
                @if($upcomingEvents->isNotEmpty())
                <h3 class="mb-3"><i class="bi bi-calendar-check text-success"></i> Prossimi Eventi ({{ $upcomingEvents->count() }})</h3>
                <div class="row mb-4">
                    @foreach($upcomingEvents as $event)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-{{ $event->status == 'published' ? 'success' : ($event->status == 'completed' ? 'secondary' : 'warning') }} text-white">
                                    <h5 class="card-title mb-0">{{ $event->title }}</h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <i class="bi bi-tag"></i> 
                                            @switch($event->event_type)
                                                @case('cultural') Culturale @break
                                                @case('recreational') Ricreativo @break
                                                @case('educational') Educativo @break
                                                @case('sports') Sportivo @break
                                                @default Altro
                                            @endswitch
                                        </small>
                                    </p>

                                    <p class="card-text">{{ \Illuminate\Support\Str::limit($event->description, 100) }}</p>

                                    <hr>

                                    <p class="mb-1">
                                        <i class="bi bi-calendar"></i> 
                                        <strong>{{ $event->event_date->format('d/m/Y') }}</strong> 
                                        alle {{ $event->event_time }}
                                    </p>

                                    <p class="mb-1">
                                        <i class="bi bi-geo-alt"></i> {{ $event->location }}
                                    </p>

                                    @if($event->cost)
                                        <p class="mb-1">
                                            <i class="bi bi-currency-euro"></i> {{ number_format($event->cost, 2) }} €
                                        </p>
                                    @else
                                        <p class="mb-1 text-success">
                                            <i class="bi bi-gift"></i> Gratuito
                                        </p>
                                    @endif

                                    <hr>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-{{ $event->status == 'published' ? 'success' : ($event->status == 'completed' ? 'secondary' : 'warning') }}">
                                            @switch($event->status)
                                                @case('draft') Bozza @break
                                                @case('published') Pubblicato @break
                                                @case('completed') Concluso @break
                                                @case('cancelled') Annullato @break
                                            @endswitch
                                        </span>

                                        @if($event->pivot && $event->pivot->registered_at)
                                            <small class="text-muted">
                                                Iscritto il {{ \Carbon\Carbon::parse($event->pivot->registered_at)->format('d/m/Y') }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('events.show', $event) }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-eye"></i> Dettagli
                                        </a>

                                        @if($event->canUnregister(auth()->user()) && $event->status == 'published')
                                            <form method="POST" action="{{ route('events.unregister', $event) }}" 
                                                  onsubmit="return confirm('Sei sicuro di volerti disiscrivere?')">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-x-circle"></i> Disiscrivi
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif

                @if($pastEvents->isNotEmpty())
                <h3 class="mb-3 mt-4"><i class="bi bi-clock-history text-muted"></i> Eventi Passati ({{ $pastEvents->count() }})</h3>
                <div class="row">
                    @foreach($pastEvents as $event)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm opacity-75">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="card-title mb-0">{{ $event->title }}</h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <i class="bi bi-tag"></i> 
                                            @switch($event->event_type)
                                                @case('cultural') Culturale @break
                                                @case('recreational') Ricreativo @break
                                                @case('educational') Educativo @break
                                                @case('sports') Sportivo @break
                                                @default Altro
                                            @endswitch
                                        </small>
                                    </p>

                                    <p class="mb-1">
                                        <i class="bi bi-calendar"></i> 
                                        <strong>{{ $event->event_date->format('d/m/Y') }}</strong> 
                                        alle {{ $event->event_time }}
                                    </p>

                                    <p class="mb-1">
                                        <i class="bi bi-geo-alt"></i> {{ $event->location }}
                                    </p>

                                    @if($event->pivot && $event->pivot->registered_at)
                                        <small class="text-muted">
                                            Partecipato il {{ \Carbon\Carbon::parse($event->pivot->registered_at)->format('d/m/Y') }}
                                        </small>
                                    @endif
                                </div>
                                <div class="card-footer">
                                    <a href="{{ route('events.show', $event) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-eye"></i> Dettagli
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
