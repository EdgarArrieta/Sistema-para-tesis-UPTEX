<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Tickets UPTEX')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4F46E5;
            --primary-dark: #4338CA;
            --secondary: #64748B;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --info: #3B82F6;
            --dark: #1E293B;
            --light: #F8FAFC;
            --sidebar-width: 260px;
            --header-height: 64px;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--light);
            color: var(--dark);
            overflow-x: hidden;
        }
        
        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, #1E293B 0%, #0F172A 100%);
            color: white;
            z-index: 1050;
            transition: transform 0.3s ease-in-out;
            overflow-y: auto;
        }
        
        /* HEADER */
        .main-header {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background: white;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            z-index: 1000;
            transition: left 0.3s ease;
        }
        
        /* CONTENIDO PRINCIPAL */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 1.5rem;
            min-height: calc(100vh - var(--header-height));
            transition: margin-left 0.3s ease;
        }

        /* CAPA OSCURA MÓVIL */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
        }

        .sidebar-header { padding: 1.5rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-logo { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; color: white; }
        .sidebar-logo i { font-size: 1.75rem; color: var(--primary); }
        .sidebar-logo-text h4 { font-size: 1.1rem; font-weight: 700; margin: 0; }
        .sidebar-logo-text p { font-size: 0.75rem; color: rgba(255,255,255,0.6); margin: 0; }

        .sidebar-nav { padding: 1rem 0; }
        .nav-section-title { padding: 1rem 1.5rem 0.5rem; font-size: 0.75rem; font-weight: 600; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 0.5px; }
        .nav-item { margin: 0.25rem 0.75rem; }
        .nav-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; color: rgba(255,255,255,0.8); text-decoration: none; border-radius: 8px; transition: all 0.2s ease; font-size: 0.9rem; }
        .nav-link:hover { background: rgba(255,255,255,0.1); color: white; }
        .nav-link.active { background: var(--primary); color: white; }

        /* RESPONSIVIDAD */
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0) !important; }
            .main-header, .main-content { margin-left: 0; left: 0; }
            .sidebar-overlay.show { display: block !important; }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('dashboard') }}" class="sidebar-logo">
                <i class="bi bi-ticket-perforated-fill"></i>
                <div class="sidebar-logo-text">
                    <h4>UPTEX Tickets</h4>
                    <p>Sistema de Soporte</p>
                </div>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section-title">Principal</div>
            <div class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i>
                    <span>
                        @if(session('usuario_rol') == 'Administrador') Panel de Administrador 
                        @elseif(session('usuario_rol') == 'Técnico') Panel de Trabajo 
                        @else Mi Dashboard @endif
                    </span>
                </a>
            </div>
            
            @if(session('usuario_rol') == 'Técnico')
                <div class="nav-section-title">Trabajo Técnico</div>
                <div class="nav-item">
                    <a href="{{ route('tickets.mis-tickets') }}" class="nav-link {{ request()->routeIs('tickets.mis-tickets') ? 'active' : '' }}">
                        <i class="bi bi-list-task"></i>
                        <span>Todos los Tickets</span>
                    </a>
                </div>
            @endif

            @if(session('usuario_rol') == 'Administrador')
                <div class="nav-section-title">Gestión</div>
                <div class="nav-item">
                    <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Usuarios</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('reportes.index') }}" class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i>
                        <span>Dashboard Global</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('tickets.index') }}" class="nav-link {{ request()->routeIs('tickets.index') ? 'active' : '' }}">
                        <i class="bi bi-ticket"></i>
                        <span>Ver Tickets</span>
                    </a>
                </div>
            @endif

            @if(session('usuario_rol') == 'Usuario Normal')
                <div class="nav-section-title">Mis Trámites</div>
                <div class="nav-item">
                    <a href="{{ route('tickets.create') }}" class="nav-link {{ request()->routeIs('tickets.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle"></i>
                        <span>Crear Ticket</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('tickets.mis-tickets') }}" class="nav-link {{ request()->routeIs('tickets.mis-tickets') ? 'active' : '' }}">
                        <i class="bi bi-ticket-detailed"></i>
                        <span>Mis Tickets</span>
                    </a>
                </div>
            @endif
            
            <div class="nav-section-title">Cuenta</div>
            <div class="nav-item">
                <a href="{{ route('perfil') }}" class="nav-link {{ request()->routeIs('perfil') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i>
                    <span>Mi Perfil</span>
                </a>
            </div>
        </nav>
    </div>
    
    <header class="main-header">
        <div class="d-flex align-items-center">
            <button class="btn btn-link d-lg-none text-dark p-0 me-3" id="btnToggleSidebar" type="button">
                <i class="bi bi-list" style="font-size: 2rem;"></i>
            </button>
            <h5 class="mb-0 d-none d-md-block">@yield('title')</h5>
        </div>
        
        <div class="user-menu dropdown">
            <div class="d-flex align-items-center" data-bs-toggle="dropdown" style="cursor: pointer;">
                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 8px;">
                    {{ strtoupper(substr(session('usuario_nombre'), 0, 1)) }}
                </div>
                <span class="d-none d-sm-inline">{{ session('usuario_nombre') }}</span>
                <i class="bi bi-chevron-down ms-1"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li><a class="dropdown-item" href="{{ route('perfil') }}"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</button>
                    </form>
                </li>
            </ul>
        </div>
    </header>
    
    <main class="main-content">
        <div class="container-fluid px-4 mt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm fw-bold" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-alert="close" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm fw-bold" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-alert="close" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <ul class="mb-0 fw-bold">
                        @foreach ($errors->all() as $error)
                            <li><i class="bi bi-x-circle me-1"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-alert="close" aria-label="Close"></button>
                </div>
            @endif
        </div>
        @yield('content')
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnToggle = document.getElementById('btnToggleSidebar');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (btnToggle) {
                btnToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    overlay.classList.toggle('show');
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>