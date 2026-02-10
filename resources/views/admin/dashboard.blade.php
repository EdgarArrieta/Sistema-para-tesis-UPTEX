@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
<div class="row">
    <!-- HEADER -->
    <div class="col-12 mb-4">
        <h2><i class="bi bi-gear me-2"></i>Panel de Administración</h2>
        <p class="text-muted">Gestión del sistema y usuarios</p>
    </div>
    
    <!-- ESTADÍSTICAS GENERALES -->
    <div class="col-md-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #DBEAFE; color: #3B82F6;">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-value" style="color: #3B82F6;">{{ $stats['total_usuarios'] ?? 0 }}</div>
            <div class="stat-label">Total Usuarios</div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #FEF3C7; color: #F59E0B;">
                <i class="bi bi-person-badge"></i>
            </div>
            <div class="stat-value" style="color: #F59E0B;">{{ $stats['tecnicos'] ?? 0 }}</div>
            <div class="stat-label">Técnicos</div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #E0E7FF; color: #6366F1;">
                <i class="bi bi-ticket"></i>
            </div>
            <div class="stat-value" style="color: #6366F1;">{{ $stats['total_tickets'] ?? 0 }}</div>
            <div class="stat-label">Total Tickets</div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: #D1FAE5; color: #10B981;">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-value" style="color: #10B981;">{{ $stats['tickets_cerrados'] ?? 0 }}</div>
            <div class="stat-label">Tickets Cerrados</div>
        </div>
    </div>
    
    <!-- ACCIONES RÁPIDAS -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Acciones Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <a href="{{ route('usuarios.create') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-person-plus me-2"></i>
                        Crear Nuevo Técnico
                    </a>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-people me-2"></i>
                        Gestionar Usuarios
                    </a>
                    <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-graph-up me-2"></i>
                        Ver Reportes
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ACTIVIDAD RECIENTE -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Actividad Reciente</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($actividad ?? [] as $item)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span>{{ $item['mensaje'] }}</span>
                            <small class="text-muted">{{ $item['tiempo'] }}</small>
                        </div>
                    </div>
                    @endforeach
                    
                    @if(count($actividad ?? []) == 0)
                    <p class="text-muted text-center py-3">No hay actividad reciente</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection