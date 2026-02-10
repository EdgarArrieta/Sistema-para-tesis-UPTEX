@extends('layouts.app')

@section('title', 'Mi Dashboard')

@section('content')
<div class="row">
    <!-- HEADER -->
    <div class="col-12 mb-4">
        <h2>¡Hola, {{ session('usuario_nombre') }}!</h2>
        <p class="text-muted">Bienvenido a tu panel de tickets</p>
    </div>
    
    <!-- BOTÓN PRINCIPAL CREAR TICKET -->
    <div class="col-12 mb-4">
        <div class="card" style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); color: white; border: none;">
            <div class="card-body p-5 text-center">
                <i class="bi bi-plus-circle" style="font-size: 4rem; margin-bottom: 1rem;"></i>
                <h3 class="mb-3">¿Necesitas ayuda?</h3>
                <p class="mb-4">Crea un nuevo ticket de soporte y nuestro equipo te ayudará</p>
                <a href="{{ route('tickets.create') }}" class="btn btn-light btn-lg">
                    <i class="bi bi-plus-circle me-2"></i>
                    Crear Nuevo Ticket
                </a>
            </div>
        </div>
    </div>
    
    <!-- ESTADÍSTICAS RÁPIDAS -->
    <div class="col-md-4 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #EEF2FF; color: #4F46E5;">
                <i class="bi bi-ticket"></i>
            </div>
            <div class="stat-value" style="color: #4F46E5;">{{ $stats['total'] ?? 0 }}</div>
            <div class="stat-label">Total de Tickets</div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #FEF3C7; color: #F59E0B;">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="stat-value" style="color: #F59E0B;">{{ $stats['abiertos'] ?? 0 }}</div>
            <div class="stat-label">En Proceso</div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #D1FAE5; color: #10B981;">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-value" style="color: #10B981;">{{ $stats['cerrados'] ?? 0 }}</div>
            <div class="stat-label">Resueltos</div>
        </div>
    </div>
    
    
<!-- MIS TICKETS RECIENTES -->
<div class="col-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-task me-2"></i>Mis Tickets Recientes</h5>
            <a href="{{ route('tickets.mis-tickets') }}" class="btn btn-sm btn-primary">Ver Todos</a>
        </div>
        <div class="card-body">
            @if(!empty($tickets) && count($tickets) > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Estado</th>
                            <th>Prioridad</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                        <tr>
                            <td><strong>#{{ $ticket->id_ticket }}</strong></td>
                            <td>{{ Str::limit($ticket->titulo, 50) }}</td>
                            <td>
                                <span class="badge badge-estado-{{ $ticket->estado->tipo ?? 'abierto' }}">
                                    {{ $ticket->estado->nombre ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-prioridad-{{ $ticket->prioridad->nivel ?? 1 }}">
                                    {{ $ticket->prioridad->nombre ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ $ticket->fecha_creacion ? $ticket->fecha_creacion->format('d/m/Y') : 'N/A' }}</td>
                            <td>
                                <a href="{{ route('tickets.show', $ticket->id_ticket) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-inbox" style="font-size: 4rem; color: #CBD5E1;"></i>
                <p class="text-muted mt-3">No has creado ningún ticket aún</p>
                <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Crear tu Primer Ticket
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection