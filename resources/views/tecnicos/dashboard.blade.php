@extends('layouts.app')

@section('title', 'Panel de Trabajo')

@section('content')
<div class="row">
    <!-- HEADER -->
    <div class="col-12 mb-4">
        <h2><i class="bi bi-clipboard-check me-2"></i>Panel de Trabajo</h2>
        <p class="text-muted">Gestiona tus tickets asignados</p>
    </div>
    
    <!-- ESTADÍSTICAS DE TRABAJO -->
    <div class="col-md-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #DBEAFE; color: #3B82F6;">
                <i class="bi bi-clipboard-data"></i>
            </div>
            <div class="stat-value" style="color: #3B82F6;">{{ $stats['asignados'] ?? 0 }}</div>
            <div class="stat-label">Asignados a Mí</div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #FEF3C7; color: #F59E0B;">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="stat-value" style="color: #F59E0B;">{{ $stats['en_proceso'] ?? 0 }}</div>
            <div class="stat-label">En Proceso</div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #D1FAE5; color: #10B981;">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-value" style="color: #10B981;">{{ $stats['resueltos_hoy'] ?? 0 }}</div>
            <div class="stat-label">Resueltos Hoy</div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #FEE2E2; color: #EF4444;">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="stat-value" style="color: #EF4444;">{{ $stats['urgentes'] ?? 0 }}</div>
            <div class="stat-label">Urgentes</div>
        </div>
    </div>
    
    <!-- TICKETS ASIGNADOS -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list-task me-2"></i>Tickets Asignados</h5>
                <a href="{{ route('tickets.asignados') }}" class="btn btn-sm btn-primary">Ver Todos</a>
            </div>
            <div class="card-body">
                @if(count($tickets) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Usuario</th>
                                <th>Prioridad</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                            <tr>
                                <td><strong>#{{ $ticket->id_ticket }}</strong></td>
                                <td>{{ Str::limit($ticket->titulo, 40) }}</td>
                                <td>{{ $ticket->usuario->nombre_completo }}</td>
                                <td>
                                    <span class="badge badge-prioridad-{{ $ticket->prioridad->nivel }}">
                                        {{ $ticket->prioridad->nombre }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-estado-{{ $ticket->estado->tipo }}">
                                        {{ $ticket->estado->nombre }}
                                    </span>
                                </td>
                                <td>{{ $ticket->fecha_creacion->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('tickets.show', $ticket->id_ticket) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('tickets.edit', $ticket->id_ticket) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
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
                    <p class="text-muted mt-3">No tienes tickets asignados en este momento</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection