@extends('layouts.app')

@section('title', 'Rendimiento de Técnicos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people"></i> Rendimiento de Técnicos</h2>
    <a href="{{ route('reportes.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Técnico</th>
                        <th>Correo</th>
                        <th>Total Asignados</th>
                        <th>Cerrados</th>
                        <th>En Proceso</th>
                        <th>Abiertos</th>
                        <th>Efectividad</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tecnicos as $tecnico)
                    <tr>
                        <td>
                            <i class="bi bi-person-badge"></i>
                            <strong>{{ $tecnico['tecnico'] }}</strong>
                        </td>
                        <td>{{ $tecnico['correo'] }}</td>
                        <td><span class="badge bg-primary">{{ $tecnico['total_asignados'] }}</span></td>
                        <td><span class="badge bg-success">{{ $tecnico['cerrados'] }}</span></td>
                        <td><span class="badge bg-warning">{{ $tecnico['en_proceso'] }}</span></td>
                        <td><span class="badge bg-info">{{ $tecnico['abiertos'] }}</span></td>
                        <td>
                            @php
                                $efectividad = $tecnico['total_asignados'] > 0 
                                    ? round(($tecnico['cerrados'] / $tecnico['total_asignados']) * 100) 
                                    : 0;
                            @endphp
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar {{ $efectividad >= 70 ? 'bg-success' : ($efectividad >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                     role="progressbar" 
                                     style="width: {{ $efectividad }}%">
                                    {{ $efectividad }}%
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No hay datos de técnicos disponibles
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Gráfica de Comparación -->
@if(count($tecnicos) > 0)
<div class="card mt-4">
    <div class="card-header">
        <i class="bi bi-bar-chart"></i> Comparación de Técnicos
    </div>
    <div class="card-body">
        <canvas id="chartTecnicos"></canvas>
    </div>
</div>
@endif

@push('scripts')
<script>
@if(count($tecnicos) > 0)
const ctxTecnicos = document.getElementById('chartTecnicos').getContext('2d');
new Chart(ctxTecnicos, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($tecnicos, 'tecnico')) !!},
        datasets: [
            {
                label: 'Cerrados',
                data: {!! json_encode(array_column($tecnicos, 'cerrados')) !!},
                backgroundColor: '#10B981'
            },
            {
                label: 'En Proceso',
                data: {!! json_encode(array_column($tecnicos, 'en_proceso')) !!},
                backgroundColor: '#F59E0B'
            },
            {
                label: 'Abiertos',
                data: {!! json_encode(array_column($tecnicos, 'abiertos')) !!},
                backgroundColor: '#3B82F6'
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
@endif
</script>
@endpush
@endsection