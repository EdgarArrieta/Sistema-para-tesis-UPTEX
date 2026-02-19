@extends('layouts.app')

@section('title', 'Panel de Administración - UPTEX')

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4 mt-2">
        <h2 class="fw-bold d-flex align-items-center">
            <i class="bi bi-gear-wide-connected me-2 text-primary"></i> Panel de Administración
        </h2>
        <p class="text-muted small">Gestión de métricas generales de UPTEX Tickets.</p>
    </div>

    <div class="row g-3 mb-4 text-center">
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100 border-bottom border-4 border-primary">
                <div class="card-body p-3">
                    <h6 class="text-muted small fw-bold text-uppercase mb-2">Usuarios</h6>
                    <h2 class="fw-bold mb-0 text-primary">{{ $stats['total_usuarios'] ?? '0' }}</h2>
                    <i class="bi bi-people fs-2 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100 border-bottom border-4 border-warning">
                <div class="card-body p-3">
                    <h6 class="text-muted small fw-bold text-uppercase mb-2 text-warning">Técnicos</h6>
                    <h2 class="fw-bold mb-0 text-warning">{{ $stats['tecnicos'] ?? '0' }}</h2>
                    <i class="bi bi-person-badge fs-2 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100 border-bottom border-4" style="border-color: #6f42c1 !important;">
                <div class="card-body p-3">
                    <h6 class="text-muted small fw-bold text-uppercase mb-2" style="color: #6f42c1;">U. Normales</h6>
                    <h2 class="fw-bold mb-0" style="color: #6f42c1;">{{ $stats['usuarios_normales'] ?? '0' }}</h2>
                    <i class="bi bi-person fs-2 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100 border-bottom border-4 border-info">
                <div class="card-body p-3">
                    <h6 class="text-muted small fw-bold text-uppercase mb-2 text-info">Tickets</h6>
                    <h2 class="fw-bold mb-0 text-info">{{ $stats['total_tickets'] ?? '0' }}</h2>
                    <i class="bi bi-ticket-perforated fs-2 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100 border-bottom border-4 border-success">
                <div class="card-body p-3">
                    <h6 class="text-muted small fw-bold text-uppercase mb-2 text-success">Abiertos</h6>
                    <h2 class="fw-bold mb-0 text-success">{{ $stats['tickets_abiertos'] ?? '0' }}</h2>
                    <i class="bi bi-envelope-open fs-2 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card border-0 shadow-sm h-100 border-bottom border-4 border-danger">
                <div class="card-body p-3">
                    <h6 class="text-muted small fw-bold text-uppercase mb-2 text-danger">Cerrados</h6>
                    <h2 class="fw-bold mb-0 text-danger">{{ $stats['tickets_cerrados'] ?? '0' }}</h2>
                    <i class="bi bi-check2-circle fs-2 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <h5 class="fw-bold mb-3 text-secondary"><i class="bi bi-tags-fill me-2"></i>Distribución por Prioridad</h5>
    <div class="row g-3 mb-4 text-center">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
                <div class="card-body d-flex justify-content-between p-3">
                    <div class="text-start">
                        <h6 class="text-muted small fw-bold mb-1">Baja</h6>
                        <h2 class="fw-bold mb-0">{{ $stats['prioridad_baja'] ?? '0' }}</h2>
                    </div>
                    <i class="bi bi-arrow-down-circle text-info fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                <div class="card-body d-flex justify-content-between p-3">
                    <div class="text-start">
                        <h6 class="text-muted small fw-bold mb-1">Media</h6>
                        <h2 class="fw-bold mb-0 text-primary">{{ $stats['prioridad_media'] ?? '0' }}</h2>
                    </div>
                    <i class="bi bi-dash-circle text-primary fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
                <div class="card-body d-flex justify-content-between p-3">
                    <div class="text-start">
                        <h6 class="text-muted small fw-bold mb-1 text-warning">Alta</h6>
                        <h2 class="fw-bold mb-0 text-warning">{{ $stats['prioridad_alta'] ?? '0' }}</h2>
                    </div>
                    <i class="bi bi-exclamation-circle text-warning fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-danger">
                <div class="card-body d-flex justify-content-between p-3">
                    <div class="text-start">
                        <h6 class="text-muted small fw-bold mb-1 text-danger">Crítica</h6>
                        <h2 class="fw-bold mb-0 text-danger">{{ $stats['prioridad_critica'] ?? '0' }}</h2>
                    </div>
                    <i class="bi bi-fire text-danger fs-1 opacity-50 animate-pulse"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Acciones Rápidas</h5>
                </div>
                <div class="card-body d-grid gap-3 pt-0">
                    <a href="{{ route('admin.tecnicos.create') }}" class="btn btn-primary py-2 fw-bold shadow-sm">
                        <i class="bi bi-person-plus-fill me-2"></i>Crear Nuevo Técnico
                    </a>
                    <a href="{{ route('admin.usuarios.create') }}" class="btn btn-outline-primary py-2 fw-bold">
                        <i class="bi bi-people-fill me-2"></i>Crear Nuevo Usuario
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-clock-history text-info me-2"></i>Actividad Reciente</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-muted py-5 pt-0">
                    <i class="bi bi-inbox fs-1 mb-3 opacity-25"></i>
                    <p class="mb-0 small">No hay actividad reciente para mostrar.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card { transition: transform 0.2s; border-radius: 12px; }
    .card:hover { transform: translateY(-3px); }
    .animate-pulse { animation: pulse 2s infinite; }
    @keyframes pulse { 0% { opacity: 0.5; } 50% { opacity: 1; } 100% { opacity: 0.5; } }
</style>
@endsection