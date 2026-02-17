@extends('layouts.app')

@section('title', 'Reportes y Estadísticas')

@section('content')
<style>
    .report-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        height: 100%;
        border: 1px solid #f0f0f0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        transition: transform 0.2s;
    }
    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }
    .report-icon {
        font-size: 3.5rem;
        margin-bottom: 1.5rem;
        color: #1E293B;
    }
    
    /* Ajustes para móviles */
    @media (max-width: 768px) {
        .report-card { padding: 1.5rem; margin-bottom: 1rem; }
        .report-icon { font-size: 2.5rem; }
    }
</style>

<div class="container-fluid">
    <div class="mb-4">
        <h2 class="fw-bold"><i class="bi bi-graph-up me-2"></i>Reportes y Estadísticas</h2>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-12 col-md-4">
            <div class="report-card">
                <i class="bi bi-calendar-event report-icon text-primary"></i>
                <h4>Tickets por Fecha</h4>
                <p>Generar reporte de tickets en un rango de fechas</p>
                <button class="btn btn-primary w-100 py-2 fw-bold">
                    <i class="bi bi-download me-2"></i>Generar
                </button>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="report-card">
                <i class="bi bi-people report-icon text-warning"></i>
                <h4>Rendimiento de Técnicos</h4>
                <p>Ver estadísticas de desempeño de técnicos</p>
                <button class="btn btn-warning w-100 py-2 fw-bold text-dark">
                    <i class="bi bi-eye me-2"></i>Ver Reporte
                </button>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="report-card">
                <i class="bi bi-file-earmark-spreadsheet report-icon text-success"></i>
                <h4>Exportar Todo a CSV</h4>
                <p>Descargar todos los tickets en formato CSV</p>
                <button class="btn btn-success w-100 py-2 fw-bold">
                    <i class="bi bi-file-earmark-arrow-down me-2"></i>Exportar
                </button>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm p-4">
        <h5 class="mb-4 fw-bold"><i class="bi bi-bar-chart me-2"></i>Estadísticas del Sistema</h5>
        <div class="row g-3">
            <div class="col-6 col-lg-3">
                <div class="p-3 rounded-3 text-center" style="background: #eff6ff;">
                    <span class="d-block h4 fw-bold mb-0" style="color: #2563eb;">14</span>
                    <small class="text-muted">Total Usuarios</small>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="p-3 rounded-3 text-center" style="background: #fffbeb;">
                    <span class="d-block h4 fw-bold mb-0" style="color: #d97706;">3</span>
                    <small class="text-muted">Técnicos</small>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="p-3 rounded-3 text-center" style="background: #f0fdf4;">
                    <span class="d-block h4 fw-bold mb-0" style="color: #16a34a;">25</span>
                    <small class="text-muted">Total Tickets</small>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="p-3 rounded-3 text-center" style="background: #f8fafc;">
                    <span class="d-block h4 fw-bold mb-0" style="color: #475569;">4</span>
                    <small class="text-muted">Cerrados</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection