@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard Admin</h1>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-calendar-event"></i> Totale Eventi</h5>
                            <h2>{{ $stats['total'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-check-circle"></i> Pubblicati</h5>
                            <h2>{{ $stats['published'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-pencil"></i> Bozze</h5>
                            <h2>{{ $stats['draft'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-flag"></i> Conclusi</h5>
                            <h2>{{ $stats['completed'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-x-circle"></i> Annullati</h5>
                            <h2>{{ $stats['cancelled'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-people"></i> Totale Iscritti</h5>
                            <h2>{{ $stats['total_participants'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-calendar-plus"></i> Prossimi Eventi</h5>
                            <h2>{{ $stats['upcoming_events'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($typeStats) && $typeStats->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Statistiche per Tipologia</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tipologia</th>
                                    <th>Numero Eventi</th>
                                    <th>Media Partecipanti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($typeStats as $stat)
                                <tr>
                                    <td>
                                        @switch($stat->event_type)
                                            @case('cultural') <i class="bi bi-palette"></i> Culturale @break
                                            @case('recreational') <i class="bi bi-emoji-smile"></i> Ricreativo @break
                                            @case('educational') <i class="bi bi-book"></i> Educativo @break
                                            @case('sports') <i class="bi bi-trophy"></i> Sportivo @break
                                            @default <i class="bi bi-tag"></i> Altro
                                        @endswitch
                                    </td>
                                    <td><strong>{{ $stat->count }}</strong></td>
                                    <td>{{ number_format($stat->avg_participants, 1) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Gestione Eventi</h4>
                    <div>
                        <a href="{{ route('admin.events.export') }}" class="btn btn-light btn-sm me-2">
                            <i class="bi bi-download"></i> Esporta CSV
                        </a>
                        <a href="{{ route('admin.events.create') }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-plus-circle"></i> Nuovo Evento
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Titolo</th>
                                    <th>Data</th>
                                    <th>Tipologia</th>
                                    <th>Stato</th>
                                    <th>Partecipanti</th>
                                    <th>Creato da</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($events as $event)
                                    <tr>
                                        <td>{{ $event->id }}</td>
                                        <td>
                                            <strong>{{ $event->title }}</strong><br>
                                            <small class="text-muted">{{ \Illuminate\Support\Str::limit($event->location, 30) }}</small>
                                        </td>
                                        <td>
                                            {{ $event->event_date->format('d/m/Y') }}<br>
                                            <small class="text-muted">{{ $event->event_time }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                @switch($event->event_type)
                                                    @case('cultural') Culturale @break
                                                    @case('recreational') Ricreativo @break
                                                    @case('educational') Educativo @break
                                                    @case('sports') Sportivo @break
                                                    @default Altro
                                                @endswitch
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $event->status == 'published' ? 'success' : ($event->status == 'draft' ? 'warning' : 'secondary') }}">
                                                @switch($event->status)
                                                    @case('draft') Bozza @break
                                                    @case('published') Pubblicato @break
                                                    @case('completed') Concluso @break
                                                    @case('cancelled') Annullato @break
                                                @endswitch
                                            </span>
                                        </td>
                                        <td>
                                            <i class="bi bi-people"></i> 
                                            {{ $event->participantsCount() }}
                                            @if($event->max_participants)
                                                / {{ $event->max_participants }}
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $event->creator->name }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('events.show', $event) }}" 
                                                   class="btn btn-info" title="Visualizza">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.events.edit', $event) }}" 
                                                   class="btn btn-warning" title="Modifica">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                @if($event->participantsCount() > 0)
                                                <a href="{{ route('admin.events.export-participants', $event) }}" 
                                                   class="btn btn-secondary" title="Esporta Partecipanti">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                @endif
                                                <form method="POST" action="{{ route('admin.events.destroy', $event) }}" 
                                                      class="d-inline" 
                                                      onsubmit="return confirm('Sei sicuro di voler eliminare questo evento?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" title="Elimina">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <em>Nessun evento presente</em>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

               
                    <div class="d-flex justify-content-center mt-3">
                        {{ $events->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
