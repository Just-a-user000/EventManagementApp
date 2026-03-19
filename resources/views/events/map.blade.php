@extends('layouts.app')

@section('title', 'Mappa Eventi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1><i class="bi bi-map"></i> Mappa Eventi</h1>
                <a href="{{ route('events.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-list"></i> Vista Lista
                </a>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <form id="mapFilters" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Tipologia</label>
                            <select class="form-select" name="type" id="filterType">
                                <option value="">Tutte</option>
                                <option value="cultural">Culturale</option>
                                <option value="recreational">Ricreativo</option>
                                <option value="educational">Educativo</option>
                                <option value="sports">Sportivo</option>
                                <option value="other">Altro</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Prezzo</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="free" id="filterFree" value="1">
                                <label class="form-check-label" for="filterFree">
                                    Solo eventi gratuiti
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Disponibilità</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="available" id="filterAvailable" value="1">
                                <label class="form-check-label" for="filterAvailable">
                                    Solo con posti disponibili
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-funnel"></i> Applica Filtri
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="map" style="height: 600px; border-radius: 8px;" class="shadow-sm"></div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let map;
    let markers = [];
    let autoRefreshInterval;
    let lastEventData = [];

    function initMap() {
        map = L.map('map').setView([41.9028, 12.4964], 6);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        loadEvents();
        startAutoRefresh();
    }

    function loadEvents(silent = false) {
        const formData = new FormData(document.getElementById('mapFilters'));
        const params = new URLSearchParams(formData);

        fetch('{{ route('api.events.map') }}?' + params.toString())
            .then(response => response.json())
            .then(events => {
                if (events.length === 0 && !silent) {
                    alert('Nessun evento trovato con i filtri selezionati');
                    return;
                }

                const hasChanges = JSON.stringify(events) !== JSON.stringify(lastEventData);
                
                if (hasChanges) {
                    lastEventData = events;
                    updateMarkers(events, silent);
                }
            })
            .catch(error => {
                console.error('Errore nel caricamento degli eventi:', error);
                if (!silent) {
                    alert('Errore nel caricamento degli eventi');
                }
            });
    }

    function updateMarkers(events, silent = false) {
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];

        const bounds = [];

        events.forEach(event => {
            const typeColors = {
                'cultural': '#6f42c1',
                'recreational': '#0dcaf0',
                'educational': '#198754',
                'sports': '#fd7e14',
                'other': '#6c757d'
            };

            const typeIcons = {
                'cultural': '🎨',
                'recreational': '🎉',
                'educational': '📚',
                'sports': '🏆',
                'other': '📌'
            };

            const icon = L.divIcon({
                html: `<div class="marker-icon" style="background-color: ${typeColors[event.event_type]}; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3); font-size: 16px; animation: markerPulse 0.5s ease-out;">${typeIcons[event.event_type]}</div>`,
                className: '',
                iconSize: [30, 30]
            });

            const marker = L.marker([event.latitude, event.longitude], { icon: icon })
                .addTo(map);

            const popupContent = `
                <div style="min-width: 200px;">
                    <h6 class="fw-bold mb-2">${event.title}</h6>
                    <p class="small mb-1">${event.description.substring(0, 100)}${event.description.length > 100 ? '...' : ''}</p>
                    <hr class="my-2">
                    <p class="small mb-1"><i class="bi bi-calendar"></i> ${event.event_date} alle ${event.event_time}</p>
                    <p class="small mb-1"><i class="bi bi-geo-alt"></i> ${event.location}</p>
                    <p class="small mb-1"><i class="bi bi-currency-euro"></i> ${event.cost}</p>
                    ${event.max_participants ? `<p class="small mb-2"><i class="bi bi-people"></i> ${event.participants_count}/${event.max_participants} partecipanti</p>` : ''}
                    <a href="${event.url}" class="btn btn-primary btn-sm w-100 text-white">Scopri di più</a>
                </div>
            `;

            marker.bindPopup(popupContent);
            markers.push(marker);
            bounds.push([event.latitude, event.longitude]);
        });

        if (bounds.length > 0 && !silent) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    }

    function startAutoRefresh() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
        
        autoRefreshInterval = setInterval(() => {
            loadEvents(true);
        }, 30000);
    }

    document.getElementById('mapFilters').addEventListener('submit', function(e) {
        e.preventDefault();
        loadEvents();
    });

    document.addEventListener('DOMContentLoaded', initMap);
</script>

<style>
@keyframes markerPulse {
    0% {
        transform: scale(0.8);
        opacity: 0.5;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.marker-icon {
    transition: transform 0.2s ease;
}

.marker-icon:hover {
    transform: scale(1.2);
}
</style>
@endsection
