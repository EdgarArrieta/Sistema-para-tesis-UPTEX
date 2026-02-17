@extends('layouts.app')

@section('title', 'Panel de Trabajo')

@section('content')
<style>
    /* Estilos para las tarjetas de estadísticas del técnico */
    .stat-card {
        background: white;
        padding: 1.25rem;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        height: 100%;
        border: 1px solid #f0f0f0;
    }
    
    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        color: #64748B;
        font-size: 0.85rem;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .table th, .table td {
            font-size: 0.85rem;
            white-space: nowrap;
        }
        .dashboard-header h2 {
            font-size: 1.5rem;
        }
    }
</style>

<div class="row">
    <div class="col-12 mb-4 dashboard-header text-center text-md-start">
        <h2><i class="bi bi-clipboard-check me-2 text-primary"></i>Panel de Trabajo</h2>
        <p class="text-muted">Gestiona tus tickets asignados</p>
    </div>
    
    <div class="col-6 col-md-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #DBEAFE; color: #3B82F6;">
                <i class="bi bi-clipboard-data"></i>
            </div>
            <div class="stat-value" style="color: #3B82F6;">{{ $stats['asignados'] ?? 0 }}</div>
            <div class="stat-label">Asignados</div>
        </div>
    </div>
    
    <div class="col-6 col-md-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #FEF3C7; color: #F59E0B;">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="stat-value" style="color: #F59E0B;">{{ $stats['en_proceso'] ?? 0 }}</div>
            <div class="stat-label">En Proceso</div>
        </div>
    </div>
    
    <div class="col-6 col-md-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #D1FAE5; color: #10B981;">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-value" style="color: #10B981;">{{ $stats['resueltos_hoy'] ?? 0 }}</div>
            <div class="stat-label">Resueltos Hoy</div>
        </div>
    </div>
    
    <div class="col-6 col-md-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #FEE2E2; color: #EF4444;">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="stat-value" style="color: #EF4444;">{{ $stats['urgentes'] ?? 0 }}</div>
            <div class="stat-label">Urgentes</div>
        </div>
    </div>
    
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-list-task me-2 text-primary"></i>Tickets Asignados</h5>
                <a href="{{ route('tickets.asignados') }}" class="btn btn-sm btn-primary px-3">Ver Todos</a>
            </div>
            <div class="card-body p-0">
                @if(isset($tickets) && count($tickets) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Título</th>
                                <th>Prioridad</th>
                                <th>Estado</th>
                                <th class="pe-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                            <tr>
                                <td class="ps-3"><strong>#{{ $ticket->id_ticket }}</strong></td>
                                <td>
                                    <div class="fw-medium text-dark">{{ Str::limit($ticket->titulo, 35) }}</div>
                                    <small class="text-muted">{{ $ticket->usuario->nombre }} {{ $ticket->usuario->apellido }}</small>
                                </td>
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
                                <td class="pe-3 text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('tickets.show', $ticket->id_ticket) }}" class="btn btn-sm btn-info text-white" title="Ver Detalle">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="{{ route('tecnicos.ver-ticket', $ticket->id_ticket) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="Ver Ficha Técnica">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">No tienes tickets asignados en este momento</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection