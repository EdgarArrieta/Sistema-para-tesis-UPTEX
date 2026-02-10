<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Tickets UPTEX')</title>
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
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
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--light);
            color: var(--dark);
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
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 4px;
        }
        
        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: white;
        }
        
        .sidebar-logo i {
            font-size: 1.75rem;
            color: var(--primary);
        }
        
        .sidebar-logo-text h4 {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
        }
        
        .sidebar-logo-text p {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.6);
            margin: 0;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-section-title {
            padding: 1rem 1.5rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .nav-item {
            margin: 0.25rem 0.75rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .nav-link.active {
            background: var(--primary);
            color: white;
        }
        
        .nav-link i {
            font-size: 1.2rem;
            width: 24px;
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
            padding: 0 2rem;
            z-index: 999;
        }
        
        .header-search {
            flex: 1;
            max-width: 500px;
        }
        
        .header-search input {
            width: 100%;
            padding: 0.6rem 1rem 0.6rem 2.5rem;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        
        .header-search input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary);
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .header-btn {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            border: none;
            background: var(--light);
            color: var(--secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .header-btn:hover {
            background: #E2E8F0;
            color: var(--dark);
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .user-menu:hover {
            background: var(--light);
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--info));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .user-info h6 {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .user-info p {
            margin: 0;
            font-size: 0.75rem;
            color: var(--secondary);
        }
        
        /* MAIN CONTENT */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 2rem;
            min-height: calc(100vh - var(--header-height));
        }
        
        /* CARDS */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #E2E8F0;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            border-radius: 12px 12px 0 0 !important;
        }
        
        /* STAT CARDS */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }
        
        .stat-label {
            color: var(--secondary);
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .stat-trend {
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }
        
        .trend-up {
            color: var(--success);
        }
        
        .trend-down {
            color: var(--danger);
        }
        
        /* BUTTONS */
        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: var(--primary);
            border: none;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        
        /* BADGES */
        .badge {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.8rem;
        }
        
        /* PRIORIDAD BADGES */
        .badge-prioridad-1 { background: #10B981; color: white; }
        .badge-prioridad-2 { background: #F59E0B; color: white; }
        .badge-prioridad-3 { background: #EF4444; color: white; }
        .badge-prioridad-4 { background: #7C2D12; color: white; }
        
        /* ESTADO BADGES */
        .badge-estado-abierto { background: #3B82F6; color: white; }
        .badge-estado-en_proceso { background: #F59E0B; color: white; }
        .badge-estado-pendiente { background: #8B5CF6; color: white; }
        .badge-estado-resuelto { background: #10B981; color: white; }
        .badge-estado-cerrado { background: #6B7280; color: white; }
        .badge-estado-cancelado { background: #EF4444; color: white; }
        
        /* ALERTS */
        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem 1.25rem;
        }
        
        /* RESPONSIVE */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-header, .main-content {
                margin-left: 0;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- SIDEBAR -->
    <div class="sidebar">
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
            <!-- NAVEGACIÓN COMÚN -->
            <div class="nav-section-title">Principal</div>
            <div class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i>
                    <span>Panel de Administrador</span>
                </a>
            </div>
            
            @if(session('usuario_rol') == 'Usuario Normal')
                <!-- MENÚ USUARIO NORMAL -->
                <div class="nav-item">
                    <a href="{{ route('tickets.create') }}" class="nav-link {{ request()->routeIs('tickets.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle"></i>
                        <span>Crear Ticket</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('tickets.mis-tickets') }}" class="nav-link {{ request()->routeIs('tickets.mis-tickets') ? 'active' : '' }}">
                        <i class="bi bi-ticket"></i>
                        <span>Mis Tickets</span>
                    </a>
                </div>
                
            @elseif(session('usuario_rol') == 'Técnico')
                <!-- MENÚ TÉCNICO -->
                <div class="nav-section-title">Trabajo</div>
                <div class="nav-item">
                    <a href="{{ route('tickets.asignados') }}" class="nav-link {{ request()->routeIs('tickets.asignados') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Tickets Asignados</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('tickets.historial') }}" class="nav-link {{ request()->routeIs('tickets.historial') ? 'active' : '' }}">
                        <i class="bi bi-list-task"></i>
                        <span>Todos los Tickets</span>
                    </a>
                </div>
                
            @elseif(session('usuario_rol') == 'Administrador')
                <!-- MENÚ ADMIN -->
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
                        <span>Dashboard</span>
                    </a>
                </div>
                <div class="nav-item">
                    <a href="{{ route('tickets.index') }}" class="nav-link {{ request()->routeIs('tickets.index') ? 'active' : '' }}">
                        <i class="bi bi-ticket"></i>
                        <span>Ver Tickets</span>
                    </a>
                </div>
            @endif
            
            <!-- MENÚ COMÚN INFERIOR -->
            <div class="nav-section-title">Cuenta</div>
            <div class="nav-item">
                <a href="{{ route('perfil') }}" class="nav-link {{ request()->routeIs('perfil') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i>
                    <span>Mi Perfil</span>
                </a>
            </div>
        </nav>
    </div>
    
    <!-- HEADER -->
    <header class="main-header">
        <div class="header-search position-relative">
            <i class="bi bi-search search-icon"></i>
            <input type="text" placeholder="Buscar tickets, usuarios...">
        </div>
        
        <div class="header-actions">
            <button class="header-btn">
                <i class="bi bi-bell"></i>
            </button>
            
            <div class="dropdown">
                <div class="user-menu" data-bs-toggle="dropdown">
                    <div class="user-avatar">
                        {{ strtoupper(substr(session('usuario_nombre'), 0, 1)) }}
                    </div>
                    <div class="user-info d-none d-md-block">
                        <h6>{{ session('usuario_nombre') }}</h6>
                        <p>{{ session('usuario_rol') }}</p>
                    </div>
                    <i class="bi bi-chevron-down"></i>
                </div>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('perfil') }}"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    
    <!-- MAIN CONTENT -->
    <main class="main-content">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        @yield('content')
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- ChartJS Data Labels Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    @stack('scripts')
</body>
</html>