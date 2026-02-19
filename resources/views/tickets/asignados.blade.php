@extends('layouts.app')

@section('title', 'Panel de Trabajo')

@section('content')
<style>
    /* Estilo Premium de Tarjetas (Estilo Administrador) */
    .stat-card-premium {
        background: white;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.07);
        text-align: center;
        border: none;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s;
        height: 100%;
    }
    .stat-card-premium:hover { transform: translateY(-5px); }
    
    /* Barras de color inferiores */
    .border-b-blue { border-bottom: 5px solid #3B82F6; }
    .border-b-yellow { border-bottom: 5px solid #F59E0B; }
    .border-b-purple { border-bottom: 5px solid #8B5CF6; }
    .border-b-green { border-bottom: 5px solid #10B981; }
    .border-b-red { border-bottom: 5px solid #EF4444; }
    .border-b-cyan { border-bottom: 5px solid #06B6D4; }

    .stat-label-caps {
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 800;
        color: #64748B;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
    }
    .stat-value-large {
        font-size: 2.2rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.5rem;
    }
    .stat-icon-bg { font-size: 1.2rem; opacity: 0.3; }
</style>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold"><i class="bi bi-person-workspace text-primary me-2"></i>Panel de Trabajo</h2>
            <p class="text-muted">Gestiona tus tickets y monitorea tu desempeño.</p>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card-premium border-b-blue">
                <div class="stat-label-caps">Total Asignados</div>
                <div class="stat-value-large text-primary">{{ $stats['totales'] }}</div>
                <i class="bi bi-folder2-open stat-icon-bg"></i>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card-premium border-b-yellow">
                <div class="stat-label-caps">En Proceso</div>
                <div class="stat-value-large text-warning">{{ $stats['en_proceso'] }}</div>
                <i class="bi bi-gear-wide-connected stat-icon-bg"></i>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card-premium border-b-purple">
                <div class="stat-label-caps">Pendientes</div>
                <div class="stat-value-large" style="color: #8B5CF6;">{{ $stats['pendientes'] }}</div>
                <i class="bi bi-pause-circle stat-icon-bg"></i>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card-premium border-b-green">
                <div class="stat-label-caps">Resueltos</div>
                <div class="stat-value-large text-success">{{ $stats['resueltos'] }}</div>
                <i class="bi bi-check2-all stat-icon-bg"></i>
            </div>
        </div>
    </div>

    <h5 class="fw-bold text-muted mb-3"><i class="bi bi-bar-chart-fill me-2"></i>Carga por Prioridad</h5>
    <div class="row mb-5">
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card-premium border-b-cyan">
                <div class="stat-label-caps">Baja</div>
                <div class="stat-value-large" style="color: #06B6D4;">{{ $stats['baja'] }}</div>
                <i class="bi bi-arrow-down-short stat-icon-bg"></i>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card-premium border-b-blue">
                <div class="stat-label-caps">Media</div>
                <div class="stat-value-large text-primary">{{ $stats['media'] }}</div>
                <i class="bi bi-dash stat-icon-bg"></i>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card-premium border-b-purple">
                <div class="stat-label-caps">Alta</div>
                <div class="stat-value-large" style="color: #8B5CF6;">{{ $stats['alta'] }}</div>
                <i class="bi bi-exclamation-lg stat-icon-bg"></i>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card-premium border-b-red">
                <div class="stat-label-caps">Crítica</div>
                <div class="stat-value-large text-danger">{{ $stats['critica'] }}</div>
                <i class="bi bi-fire stat-icon-bg"></i>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-list-stars me-2 text-primary"></i>Tickets por Atender</h5>
        </div>
        <div class="card-body p-0">
            @if(count($tickets) > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Folio</th>
                            <th>Título del Ticket</th>
                            <th>Prioridad</th>
                            <th>Estado Actual</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                        <tr>
                            <td class="ps-4"><strong>#{{ $ticket->id_ticket }}</strong></td>
                            <td>
                                <div class="fw-bold text-dark">{{ $ticket->titulo }}</div>
                                <small class="text-muted">{{ $ticket->usuario->nombre }} {{ $ticket->usuario->apellido }}</small>
                            </td>
                            <td>
                                <span class="badge rounded-pill border text-dark p-2">
                                    {{ $ticket->prioridad->nombre }}
                                </span>
                            </td>
                            <td>
                                <span class="badge p-2 bg-{{ $ticket->estado->tipo == 'en_proceso' ? 'warning text-dark' : ($ticket->estado->tipo == 'pendiente' ? 'info' : 'primary') }}">
                                    {{ $ticket->estado->nombre }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('tickets.show', $ticket->id_ticket) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                    <i class="bi bi-pencil-square me-1"></i> Gestionar
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-check-circle text-success display-4"></i>
                <p class="text-muted mt-3">¡Todo al día! No tienes tickets pendientes.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection