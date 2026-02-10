@extends('layouts.app')

@section('title', 'Reporte por Fecha')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-calendar-range"></i> Reporte de Tickets por Fecha</h2>
    <a href="{{ route('reportes.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<!-- Resumen -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-info-circle"></i> Resumen del Período
        <span class="badge bg-primary ms-2">
            {{ \Carbon\Carbon::parse($request->fecha_inicio)->format('d/m/Y') }} - 
            {{ \Carbon\Carbon::parse($request->fecha_fin)->format('d/m/Y') }}
        </span>
    </div>
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-3">
                <h3 class="text-primary">{{ $resumen['total'] ?? 0 }}</h3>
                <p class="text-muted mb-0">Total Tickets</p>
            </div>
            <div class="col-md-3">
                <h3 class="text-success">{{ $resumen['abiertos'] ?? 0 }}</h3>
                <p class="text-muted mb-0">Abiertos</p>
            </div>
            <div class="col-md-3">
                <h3 class="text-secondary">{{ $resumen['cerrados'] ?? 0 }}</h3>
                <p class="text-muted mb-0">Cerrados</p>
            </div>
            <div class="col-md-3">
                @php
                    $efectividad = isset($resumen['total']) && $resumen['total'] > 0 
                        ? round(($resumen['cerrados'] / $resumen['total']) * 100) 
                        : 0;
                @endphp
                <h3 class="text-info">{{ $efectividad }}%</h3>
                <p class="text-muted mb-0">Efectividad</p>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Tickets -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-table"></i> Detalle de Tickets</span>
        <button class="btn btn-sm btn-success" onclick="window.print()">
            <i class="bi bi-printer"></i> Imprimir
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Usuario</th>
                        <th>Área</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Fecha Creación</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td>#{{ $ticket['id_ticket'] }}</td>
                        <td>{{ Str::limit($ticket['titulo'], 50) }}</td>
                        <td>{{ $ticket['usuario']['nombre_completo'] ?? 'N/A' }}</td>
                        <td>{{ $ticket['area']['nombre'] ?? 'N/A' }}</td>
                        <td>
                            <span class="badge badge-prioridad-{{ $ticket['prioridad']['nivel'] ?? 1 }}">
                                {{ $ticket['prioridad']['nombre'] ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-estado-{{ $ticket['estado']['tipo'] ?? 'abierto' }}">
                                {{ $ticket['estado']['nombre'] ?? 'N/A' }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($ticket['fecha_creacion'])->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No hay tickets en este período
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection