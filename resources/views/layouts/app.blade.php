<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Event Management Hub')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark @auth @if(auth()->user()->isAdmin()) bg-dark @else bg-primary @endif @else bg-primary @endauth shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('events.index') }}">
                <i class="bi bi-calendar-event fs-4"></i>
                @auth
                    @if(auth()->user()->isAdmin())
                        <span class="ms-2">Admin Panel</span>
                    @else
                        <span class="ms-2">Eventi</span>
                    @endif
                @else
                    <span class="ms-2">Eventi</span>
                @endauth
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.events.create') ? 'active' : '' }}" href="{{ route('admin.events.create') }}">
                                    <i class="bi bi-plus-circle"></i> Crea Evento
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('events.index') ? 'active' : '' }}" href="{{ route('events.index') }}">
                                    <i class="bi bi-list"></i> Tutti gli Eventi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('events.map') ? 'active' : '' }}" href="{{ route('events.map') }}">
                                    <i class="bi bi-map"></i> Mappa
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('events.index') ? 'active' : '' }}" href="{{ route('events.index') }}">
                                    <i class="bi bi-list"></i> Eventi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('events.map') ? 'active' : '' }}" href="{{ route('events.map') }}">
                                    <i class="bi bi-map"></i> Mappa
                                </a>
                            </li>
                        @endif
                        
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('my-events') }}">
                                <i class="bi bi-person-circle"></i> I Miei Eventi
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="{{ route('notifications.index') }}">
                                <i class="bi bi-bell"></i> Notifiche
                                @if(auth()->user()->unreadNotifications()->count() > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ auth()->user()->unreadNotifications()->count() }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                {{ auth()->user()->name }}
                                @if(auth()->user()->isAdmin())
                                    <span class="badge bg-warning text-dark ms-1">Admin</span>
                                @else
                                    <span class="badge bg-light text-dark ms-1">Utente</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Registrati</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

   
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif


    <main class="@yield('main-class', 'py-4')">
        @yield('content')
    </main>


    <footer class="bg-light text-center text-muted py-4 mt-5">
        <div class="container">
            <p class="mb-0">&copy; 2026 Event Management Hub - Progetto di Programmazione Web</p>
        </div>
    </footer>

    <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @auth
    <script>
        let ws = null;
        const userId = {{ auth()->id() }};
        const wsUrl = 'ws://localhost:8080';
        
        function connectWebSocket() {
            ws = new WebSocket(wsUrl);
            
            ws.onopen = function() {
                console.log('WebSocket connected');
                ws.send(JSON.stringify({
                    type: 'register',
                    userId: userId
                }));
            };
            
            ws.onmessage = function(event) {
                const message = JSON.parse(event.data);
                
                if (message.type === 'notification') {
                    showToast(message.data);
                    updateNotificationBadge();
                }
            };
            
            ws.onerror = function(error) {
                console.error('WebSocket error:', error);
            };
            
            ws.onclose = function() {
                console.log('WebSocket disconnected. Reconnecting in 5 seconds...');
                setTimeout(connectWebSocket, 5000);
            };
        }
        
        function showToast(data) {
            const toastContainer = document.getElementById('toast-container');
            const toastId = 'toast-' + Date.now();
            
            const toastHtml = `
                <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-primary text-white">
                        <i class="bi bi-bell-fill me-2"></i>
                        <strong class="me-auto">${data.title || 'Notifica'}</strong>
                        <small>Adesso</small>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">
                        ${data.message}
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 5000 });
            toast.show();
            
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });
        }
        
        function updateNotificationBadge() {
            const badge = document.querySelector('.nav-link .badge.bg-danger');
            if (badge) {
                const currentCount = parseInt(badge.textContent) || 0;
                badge.textContent = currentCount + 1;
            } else {
                const notifLink = document.querySelector('a[href="{{ route('notifications.index') }}"]');
                if (notifLink) {
                    notifLink.innerHTML += '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">1</span>';
                }
            }
        }
        
        connectWebSocket();
    </script>
    @endauth
    
    @yield('scripts')
</body>
</html>
