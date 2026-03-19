@extends('layouts.app')

@section('title', 'Eventi Disponibili')

@section('main-class', '')

@section('content')
<section class="hero-section text-white text-center">
    <div class="hero-overlay"></div>
    <div class="container position-relative">
        <h1 class="hero-title display-4 fw-bold mb-3">
            <i class="bi bi-calendar-event me-2"></i>Event Management Hub
        </h1>
        <p class="hero-subtitle lead mb-4">Scopri, partecipa e vivi esperienze uniche nella tua città</p>
        
      
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <form method="GET" action="{{ route('events.index') }}" class="hero-search-form">
                    <div class="input-group input-group-lg shadow-lg">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-0" name="search" 
                               value="{{ request('search') }}" placeholder="Cerca eventi per titolo, descrizione o luogo...">
                        <select class="form-select border-0 hero-select" name="type" style="max-width: 180px;">
                            <option value="">Tutte</option>
                            <option value="cultural" {{ request('type') == 'cultural' ? 'selected' : '' }}>Culturale</option>
                            <option value="recreational" {{ request('type') == 'recreational' ? 'selected' : '' }}>Ricreativo</option>
                            <option value="educational" {{ request('type') == 'educational' ? 'selected' : '' }}>Educativo</option>
                            <option value="sports" {{ request('type') == 'sports' ? 'selected' : '' }}>Sportivo</option>
                            <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Altro</option>
                        </select>
                        <button type="submit" class="btn btn-warning fw-bold px-4">
                            <i class="bi bi-search"></i> Cerca
                        </button>
                    </div>
               
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-sm btn-outline-light" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                            <i class="bi bi-funnel"></i> Filtri Avanzati
                        </button>
                        <a href="{{ route('events.map') }}" class="btn btn-sm btn-outline-light ms-2">
                            <i class="bi bi-map"></i> Visualizza Mappa
                        </a>
                    </div>
                    
                
                    <div class="collapse mt-3" id="advancedFilters">
                        <div class="card bg-white text-dark">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label small">Prezzo</label>
                                        <select class="form-select form-select-sm" name="price_filter">
                                            <option value="">Tutti</option>
                                            <option value="free" {{ request('price_filter') == 'free' ? 'selected' : '' }}>Gratuiti</option>
                                            <option value="paid" {{ request('price_filter') == 'paid' ? 'selected' : '' }}>A pagamento</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Data da</label>
                                        <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">Data a</label>
                                        <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">&nbsp;</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="available_only" value="1" id="availableOnly" {{ request('available_only') ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="availableOnly">
                                                Solo con posti disponibili
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="open_registration" value="1" id="openReg" {{ request('open_registration') ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="openReg">
                                                Iscrizioni aperte
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

       
        <div class="row justify-content-center mt-4 g-3">
            <div class="col-auto">
                <div class="hero-stat">
                    <i class="bi bi-calendar-check"></i>
                    <span>{{ $events->total() }} Eventi</span>
                </div>
            </div>
            @auth
            <div class="col-auto">
                <div class="hero-stat">
                    <i class="bi bi-person-check"></i>
                    <span>{{ auth()->user()->registeredEvents()->count() }} Iscrizioni</span>
                </div>
            </div>
            @endauth
        </div>
    </div>
</section>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0"><i class="bi bi-fire text-danger"></i> Prossimi Eventi</h2>
                <div>
                    @if(request()->hasAny(['search', 'type', 'price_filter', 'date_from', 'date_to', 'available_only', 'open_registration']))
                        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle"></i> Rimuovi filtri
                        </a>
                    @endif
                    <a href="{{ route('events.map') }}" class="btn btn-outline-primary btn-sm ms-2">
                        <i class="bi bi-map"></i> Mappa
                    </a>
                </div>
            </div>

            @if($events->isEmpty())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Nessun evento disponibile al momento.
                </div>
            @else
                <div class="row">
                    @foreach($events as $event)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-0 shadow-sm event-card">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <span class="badge rounded-pill event-type-badge event-type-{{ $event->event_type }}">
                                            @switch($event->event_type)
                                                @case('cultural') <i class="bi bi-palette"></i> Culturale @break
                                                @case('recreational') <i class="bi bi-emoji-smile"></i> Ricreativo @break
                                                @case('educational') <i class="bi bi-book"></i> Educativo @break
                                                @case('sports') <i class="bi bi-trophy"></i> Sportivo @break
                                                @default <i class="bi bi-tag"></i> Altro
                                            @endswitch
                                        </span>
                                        @if($event->isRegistrationOpen())
                                            <span class="badge rounded-pill bg-success"><i class="bi bi-check-circle"></i> Aperto</span>
                                        @else
                                            <span class="badge rounded-pill bg-secondary"><i class="bi bi-x-circle"></i> Chiuso</span>
                                        @endif
                                    </div>

                                    <h5 class="card-title fw-bold mb-2">{{ $event->title }}</h5>
                                    <p class="card-text text-muted small mb-3">{{ \Illuminate\Support\Str::limit($event->description, 100) }}</p>
                                    
                                    <div class="event-info-list">
                                        <div class="event-info-item">
                                            <i class="bi bi-calendar3 text-primary"></i> 
                                            <strong>{{ $event->event_date->format('d/m/Y') }}</strong> 
                                            <span class="text-muted">ore {{ $event->event_time }}</span>
                                        </div>
                                        <div class="event-info-item">
                                            <i class="bi bi-geo-alt-fill text-danger"></i> 
                                            {{ $event->location }}
                                        </div>
                                        <div class="event-info-item">
                                            @if($event->cost)
                                                <i class="bi bi-currency-euro text-warning"></i> 
                                                <strong>{{ number_format($event->cost, 2) }} €</strong>
                                            @else
                                                <i class="bi bi-gift-fill text-success"></i> 
                                                <span class="text-success fw-bold">Gratuito</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($event->max_participants)
                                        <div class="mt-3">
                                            <div class="d-flex justify-content-between small mb-1">
                                                <span class="text-muted"><i class="bi bi-people-fill"></i> Partecipanti</span>
                                                <span class="fw-bold">{{ $event->participantsCount() }}/{{ $event->max_participants }}</span>
                                            </div>
                                            @php $percent = ($event->participantsCount() / $event->max_participants) * 100; @endphp
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar {{ $percent >= 100 ? 'bg-danger' : ($percent >= 75 ? 'bg-warning' : 'bg-success') }}" 
                                                     style="width: {{ min($percent, 100) }}%"></div>
                                            </div>
                                            @if($event->isFull())
                                                <small class="text-danger fw-bold mt-1 d-block">Evento al completo!</small>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div class="card-footer bg-transparent border-0 p-4 pt-0">
                                    <a href="{{ route('events.show', $event) }}" class="btn btn-primary w-100 rounded-pill">
                                        <i class="bi bi-arrow-right-circle"></i> Scopri di più
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
