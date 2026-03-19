@extends('layouts.app')

@section('title', 'Notifiche')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="bi bi-bell"></i> Le Mie Notifiche</h1>
                <span class="badge bg-primary rounded-pill">{{ $unreadCount }} non lette</span>
            </div>

            @if($notifications->isEmpty())
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Non hai ancora notifiche.
                </div>
            @else
                <div class="list-group">
                    @foreach($notifications as $notification)
                        <div class="list-group-item {{ $notification->read_at ? '' : 'list-group-item-primary' }}">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    @php
                                        $data = $notification->data;
                                        $icon = match($data['type'] ?? 'default') {
                                            'reminder' => 'bi-alarm',
                                            'status_change' => 'bi-exclamation-triangle',
                                            'deadline' => 'bi-clock-history',
                                            default => 'bi-info-circle'
                                        };
                                    @endphp
                                    
                                    <h6 class="mb-1">
                                        <i class="bi {{ $icon }}"></i>
                                        {{ $data['message'] ?? 'Notifica' }}
                                    </h6>
                                    
                                    @if(isset($data['event_title']))
                                        <p class="mb-1 text-muted small">
                                            Evento: <strong>{{ $data['event_title'] }}</strong>
                                        </p>
                                    @endif
                                    
                                    <small class="text-muted">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                
                                <div class="ms-3">
                                    @if(isset($data['event_id']))
                                        <a href="{{ route('events.show', $data['event_id']) }}" 
                                           class="btn btn-sm btn-outline-primary me-2">
                                            <i class="bi bi-eye"></i> Vedi
                                        </a>
                                    @endif
                                    
                                    @if(!$notification->read_at)
                                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}" 
                                              class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-check"></i> Segna come letta
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
