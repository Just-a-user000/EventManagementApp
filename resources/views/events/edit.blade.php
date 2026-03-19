@extends('layouts.app')

@section('title', 'Modifica Evento')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning">
                    <h3 class="mb-0"><i class="bi bi-pencil"></i> Modifica Evento</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.events.update', $event) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Titolo *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $event->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrizione *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="5" required>{{ old('description', $event->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="event_type" class="form-label">Tipologia *</label>
                                <select class="form-select @error('event_type') is-invalid @enderror" 
                                        id="event_type" name="event_type" required>
                                    <option value="">Seleziona...</option>
                                    <option value="cultural" {{ old('event_type', $event->event_type) == 'cultural' ? 'selected' : '' }}>Culturale</option>
                                    <option value="recreational" {{ old('event_type', $event->event_type) == 'recreational' ? 'selected' : '' }}>Ricreativo</option>
                                    <option value="educational" {{ old('event_type', $event->event_type) == 'educational' ? 'selected' : '' }}>Educativo</option>
                                    <option value="sports" {{ old('event_type', $event->event_type) == 'sports' ? 'selected' : '' }}>Sportivo</option>
                                    <option value="other" {{ old('event_type', $event->event_type) == 'other' ? 'selected' : '' }}>Altro</option>
                                </select>
                                @error('event_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Stato *</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="draft" {{ old('status', $event->status) == 'draft' ? 'selected' : '' }}>Bozza</option>
                                    <option value="published" {{ old('status', $event->status) == 'published' ? 'selected' : '' }}>Pubblicato</option>
                                    <option value="completed" {{ old('status', $event->status) == 'completed' ? 'selected' : '' }}>Concluso</option>
                                    <option value="cancelled" {{ old('status', $event->status) == 'cancelled' ? 'selected' : '' }}>Annullato</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Luogo *</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                   id="location" name="location" value="{{ old('location', $event->location) }}" 
                                   autocomplete="off" required>
                            <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $event->latitude) }}">
                            <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $event->longitude) }}">
                            <div id="location-suggestions" class="list-group position-absolute" style="z-index: 1000; display: none;"></div>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Inizia a digitare per cercare un indirizzo (le coordinate verranno compilate automaticamente)</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="event_date" class="form-label">Data Evento *</label>
                                <input type="date" class="form-control @error('event_date') is-invalid @enderror" 
                                       id="event_date" name="event_date" value="{{ old('event_date', $event->event_date->format('Y-m-d')) }}" required>
                                @error('event_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="event_time" class="form-label">Ora Evento *</label>
                                <input type="time" class="form-control @error('event_time') is-invalid @enderror" 
                                       id="event_time" name="event_time" value="{{ old('event_time', $event->event_time) }}" required>
                                @error('event_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="registration_deadline" class="form-label">Scadenza Iscrizioni *</label>
                            <input type="datetime-local" class="form-control @error('registration_deadline') is-invalid @enderror" 
                                   id="registration_deadline" name="registration_deadline" 
                                   value="{{ old('registration_deadline', $event->registration_deadline->format('Y-m-d\TH:i')) }}" required>
                            @error('registration_deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="max_participants" class="form-label">Numero Massimo Partecipanti</label>
                                <input type="number" class="form-control @error('max_participants') is-invalid @enderror" 
                                       id="max_participants" name="max_participants" value="{{ old('max_participants', $event->max_participants) }}" min="1">
                                @error('max_participants')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Attualmente: {{ $event->participantsCount() }} iscritti</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="cost" class="form-label">Costo (€)</label>
                                <input type="number" class="form-control @error('cost') is-invalid @enderror" 
                                       id="cost" name="cost" value="{{ old('cost', $event->cost) }}" min="0" step="0.01">
                                @error('cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Note</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes', $event->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('events.show', $event) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Annulla
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-circle"></i> Salva Modifiche
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
#location-suggestions {
    max-height: 300px;
    overflow-y: auto;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

#location-suggestions .list-group-item {
    cursor: pointer;
    border-left: 3px solid transparent;
}

#location-suggestions .list-group-item:hover {
    background-color: #f8f9fa;
    border-left-color: #0d6efd;
}
</style>

<script>
let searchTimeout;
const locationInput = document.getElementById('location');
const suggestionsDiv = document.getElementById('location-suggestions');
const latitudeInput = document.getElementById('latitude');
const longitudeInput = document.getElementById('longitude');

locationInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();
    
    if (query.length < 3) {
        suggestionsDiv.style.display = 'none';
        return;
    }
    
    searchTimeout = setTimeout(() => {
        searchLocation(query);
    }, 500);
});

function searchLocation(query) {
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=it&limit=5&addressdetails=1`;
    
    fetch(url, {
        headers: {
            'User-Agent': 'EventManagementApp/1.0'
        }
    })
    .then(response => response.json())
    .then(data => {
        displaySuggestions(data);
    })
    .catch(error => {
        console.error('Errore ricerca indirizzo:', error);
    });
}

function displaySuggestions(results) {
    suggestionsDiv.innerHTML = '';
    
    if (results.length === 0) {
        suggestionsDiv.style.display = 'none';
        return;
    }
    
    results.forEach(result => {
        const item = document.createElement('a');
        item.className = 'list-group-item list-group-item-action';
        
        const displayName = result.display_name;
        const icon = getLocationIcon(result.type);
        
        item.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi ${icon} me-2"></i>
                <div>
                    <div class="fw-bold">${result.name || result.address.city || result.address.town}</div>
                    <small class="text-muted">${displayName}</small>
                </div>
            </div>
        `;
        
        item.addEventListener('click', () => {
            selectLocation(result);
        });
        
        suggestionsDiv.appendChild(item);
    });
    
    suggestionsDiv.style.display = 'block';
}

function selectLocation(result) {
    locationInput.value = result.display_name;
    
    if (latitudeInput && longitudeInput) {
        latitudeInput.value = parseFloat(result.lat).toFixed(6);
        longitudeInput.value = parseFloat(result.lon).toFixed(6);
    }
    
    suggestionsDiv.style.display = 'none';
}

function getLocationIcon(type) {
    const icons = {
        'city': 'bi-building',
        'town': 'bi-buildings',
        'village': 'bi-house',
        'municipality': 'bi-geo-alt',
        'administrative': 'bi-map',
        'tourism': 'bi-star',
        'amenity': 'bi-pin-map'
    };
    return icons[type] || 'bi-geo-alt-fill';
}

document.addEventListener('click', function(e) {
    if (!locationInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
        suggestionsDiv.style.display = 'none';
    }
});
</script>
@endsection
