@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">{{ $event->title }}</h2>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge bg-{{ $event->status == 'published' ? 'success' : 'secondary' }} me-2">
                            @switch($event->status)
                                @case('draft') Bozza @break
                                @case('published') Pubblicato @break
                                @case('completed') Concluso @break
                                @case('cancelled') Annullato @break
                            @endswitch
                        </span>
                        <span class="badge bg-info">
                            @switch($event->event_type)
                                @case('cultural') Culturale @break
                                @case('recreational') Ricreativo @break
                                @case('educational') Educativo @break
                                @case('sports') Sportivo @break
                                @default Altro
                            @endswitch
                        </span>
                    </div>

                    <h5><i class="bi bi-info-circle"></i> Descrizione</h5>
                    <p class="text-justify">{{ $event->description }}</p>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="bi bi-calendar-event"></i> Data e Ora</h5>
                            <p>
                                <strong>{{ $event->event_date->format('d/m/Y') }}</strong> 
                                alle <strong>{{ $event->event_time }}</strong>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="bi bi-geo-alt"></i> Luogo</h5>
                            <p>{{ $event->location }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="bi bi-clock"></i> Scadenza Iscrizioni</h5>
                            <p>{{ $event->registration_deadline->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="bi bi-currency-euro"></i> Costo</h5>
                            <p>
                                @if($event->cost)
                                    {{ number_format($event->cost, 2) }} €
                                @else
                                    <span class="text-success">Gratuito</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($event->max_participants)
                        <hr>
                        <h5><i class="bi bi-people"></i> Partecipanti</h5>
                        <div class="progress mb-2" style="height: 25px;">
                            @php
                                $percentage = ($event->participantsCount() / $event->max_participants) * 100;
                            @endphp
                            <div class="progress-bar {{ $event->isFull() ? 'bg-danger' : 'bg-success' }}" 
                                 role="progressbar" 
                                 style="width: {{ $percentage }}%"
                                 aria-valuenow="{{ $event->participantsCount() }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="{{ $event->max_participants }}">
                                {{ $event->participantsCount() }}/{{ $event->max_participants }}
                            </div>
                        </div>
                        @if($event->availableSpots() !== null)
                            <p class="text-muted">
                                Posti disponibili: <strong>{{ $event->availableSpots() }}</strong>
                            </p>
                        @endif
                    @endif

                    @if($event->notes)
                        <hr>
                        <h5><i class="bi bi-sticky"></i> Note</h5>
                        <p class="text-muted">{{ $event->notes }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Registration Card -->
            @auth
                <div class="card shadow mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-person-check"></i> Iscrizione</h5>
                    </div>
                    <div class="card-body">
                        @if(auth()->user()->isRegisteredFor($event))
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle"></i> Sei iscritto a questo evento!
                            </div>
                            
                            @if($event->canUnregister(auth()->user()))
                                <form method="POST" action="{{ route('events.unregister', $event) }}" 
                                      onsubmit="return confirm('Sei sicuro di volerti disiscrivere?')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="bi bi-x-circle"></i> Disiscrivi
                                    </button>
                                </form>
                                <small class="text-muted d-block mt-2">
                                    Puoi disiscriverti fino a 24 ore prima dell'evento
                                </small>
                            @else
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i> 
                                    Non è più possibile disiscriversi
                                </div>
                            @endif
                        @else
                            @if($event->isRegistrationOpen())
                                <form method="POST" action="{{ route('events.register', $event) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-check-circle"></i> Iscriviti
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i> 
                                    Le iscrizioni sono chiuse
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @else
                <div class="card shadow mb-3">
                    <div class="card-body text-center">
                        <p><i class="bi bi-info-circle"></i> Effettua il login per iscriverti</p>
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </div>
                </div>
            @endauth

            <!-- Admin Actions -->
            @auth
                @if(auth()->user()->isAdmin())
                    <div class="card shadow">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0"><i class="bi bi-tools"></i> Azioni Admin</h5>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-warning w-100 mb-2">
                                <i class="bi bi-pencil"></i> Modifica
                            </a>
                            <form method="POST" action="{{ route('admin.events.destroy', $event) }}" 
                                  onsubmit="return confirm('Sei sicuro di voler eliminare questo evento?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="bi bi-trash"></i> Elimina
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endauth
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <a href="{{ route('events.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Torna agli eventi
            </a>
        </div>
    </div>
</div>
@endsection
