@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
<style>
    /* Estilos para que las tarjetas de estadísticas sean flexibles y se vean modernas */
    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        height: 100%;
        border: 1px solid #f0f0f0;
        transition: transform 0.2s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: #64748B;
        font-size: 0.9rem;
        font-weight: 500;
    }

    /* Ajustes específicos para móviles */
    @media (max-width: 768px) {
        .dashboard-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .dashboard-header h2 {
            font-size: 1.5rem;
        }
        /* Ajuste para que los textos de actividad no se encimen */
        .list-group-item .d-flex {
            flex-direction: column !important;
            align-items: flex-start !important;
        }
        .list-group-item small {
            margin-top: 5px;
            display: block;
        }
    }
</style>

<div class="row">
    <div class="col-12 mb-4 dashboard-header">
        <h2><i class="bi bi-gear me-2 text-primary"></i>Panel de Administración</h2>
        <p class="text-muted">Gestión del sistema y usuarios</p>
    </div>
    
    <div class="col-6 col-lg-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #DBEAFE; color: #3B82F6;">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-value" style="color: #3B82F6;">{{ $stats['total_usuarios'] ?? 0 }}</div>
            <div class="stat-label">Total Usuarios</div>
        </div>
    </div>
    
    <div class="col-6 col-lg-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #FEF3C7; color: #F59E0B;">
                <i class="bi bi-person-badge"></i>
            </div>
            <div class="stat-value" style="color: #F59E0B;">{{ $stats['tecnicos'] ?? 0 }}</div>
            <div class="stat-label">Técnicos</div>
        </div>
    </div>
    
    <div class="col-6 col-lg-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #E0E7FF; color: #6366F1;">
                <i class="bi bi-ticket"></i>
            </div>
            <div class="stat-value" style="color: #6366F1;">{{ $stats['total_tickets'] ?? 0 }}</div>
            <div class="stat-label">Total Tickets</div>
        </div>
    </div>
    
    <div class="col-6 col-lg-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #D1FAE5; color: #10B981;">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-value" style="color: #10B981;">{{ $stats['tickets_cerrados'] ?? 0 }}</div>
            <div class="stat-label">Tickets Cerrados</div>
        </div>
    </div>
    
    <div class="col-12 col-xl-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-4">
                <h5 class="mb-0 fw-bold"><i class="bi bi-lightning-charge me-2 text-warning"></i>Acciones Rápidas</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-grid gap-3">
                    <a href="{{ route('usuarios.create') }}" class="btn btn-primary btn-lg py-3 border-0 shadow-sm" style="background: var(--primary);">
                        <i class="bi bi-person-plus me-2"></i>
                        Crear Nuevo Técnico
                    </a>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-primary btn-lg py-3 border-2">
                        <i class="bi bi-people me-2"></i>
                        Gestionar Usuarios
                    </a>
                    <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary btn-lg py-3 border-2">
                        <i class="bi bi-graph-up me-2"></i>
                        Ver Reportes
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-xl-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-4">
                <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-info"></i>Actividad Reciente</h5>
            </div>
            <div class="card-body p-4">
                <div class="list-group list-group-flush">
                    @forelse($actividad ?? [] as $item)
                    <div class="list-group-item px-0 border-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-dark fw-medium">{{ $item['mensaje'] }}</span>
                            <small class="badge bg-light text-muted fw-normal">{{ $item['tiempo'] }}</small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted display-4"></i>
                        <p class="text-muted mt-3">No hay actividad reciente</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection