@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-graph-up"></i> Reportes y Estadísticas</h2>
</div>

<!-- Opciones de Reporte -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-calendar-range" style="font-size: 3rem; color: var(--primary-color);"></i>
                <h5 class="mt-3">Tickets por Fecha</h5>
                <p class="text-muted">Generar reporte de tickets en un rango de fechas</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reporteFechaModal">
                    <i class="bi bi-download"></i> Generar
                </button>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-people" style="font-size: 3rem; color: var(--warning-color);"></i>
                <h5 class="mt-3">Rendimiento de Técnicos</h5>
                <p class="text-muted">Ver estadísticas de desempeño de técnicos</p>
                <a href="{{ route('reportes.rendimiento') }}" class="btn btn-warning">
                    <i class="bi bi-eye"></i> Ver Reporte
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-file-earmark-spreadsheet" style="font-size: 3rem; color: var(--success-color);"></i>
                <h5 class="mt-3">Exportar Todo a CSV</h5>
                <p class="text-muted">Descargar todos los tickets en formato CSV</p>
                <a href="{{ route('reportes.exportar') }}" class="btn btn-success">
                    <i class="bi bi-download"></i> Exportar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas Generales -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-bar-chart-line"></i> Estadísticas del Sistema
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="text-center p-3 border rounded" style="background-color: #f0f4ff;">
                    <h3 class="text-primary">{{ $stats['total_usuarios'] ?? 0 }}</h3>
                    <p class="text-muted mb-0"><i class="bi bi-people"></i> Total Usuarios</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="text-center p-3 border rounded" style="background-color: #fff4f0;">
                    <h3 class="text-warning">{{ $stats['tecnicos'] ?? 0 }}</h3>
                    <p class="text-muted mb-0"><i class="bi bi-tools"></i> Técnicos</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="text-center p-3 border rounded" style="background-color: #f0fff4;">
                    <h3 class="text-success">{{ $stats['total_tickets'] ?? 0 }}</h3>
                    <p class="text-muted mb-0"><i class="bi bi-ticket"></i> Total Tickets</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="text-center p-3 border rounded" style="background-color: #f4f0ff;">
                    <h3 class="text-secondary">{{ $stats['tickets_cerrados'] ?? 0 }}</h3>
                    <p class="text-muted mb-0"><i class="bi bi-check-circle"></i> Tickets Cerrados</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Segmentadores de Datos -->
<div class="row mb-4">
    <!-- Segmentador Área -->
    <div class="col-md-4">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body">
                <h6 class="card-title mb-3"><i class="bi bi-building"></i> Área</h6>
                <select class="form-select" name="area_id" id="areaFilter">
                    <option value="">Todas</option>
                    @foreach($areas ?? [] as $area)
                    <option value="{{ $area->id_area }}" {{ $area_id == $area->id_area ? 'selected' : '' }}>
                        {{ $area->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    
    <!-- Segmentador Estado -->
    <div class="col-md-4">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body">
                <h6 class="card-title mb-3"><i class="bi bi-info-circle"></i> Estado</h6>
                <select class="form-select" name="estado_id" id="estadoFilter">
                    <option value="">Todos</option>
                    @foreach($estados ?? [] as $estado)
                    <option value="{{ $estado->id_estado }}" {{ $estado_id == $estado->id_estado ? 'selected' : '' }}>
                        {{ $estado->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    
    <!-- Segmentador Prioridad -->
    <div class="col-md-4">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body">
                <h6 class="card-title mb-3"><i class="bi bi-flag"></i> Prioridad</h6>
                <select class="form-select" name="prioridad_id" id="prioridadFilter">
                    <option value="">Todas</option>
                    @foreach($prioridades ?? [] as $prioridad)
                    <option value="{{ $prioridad->id_prioridad }}" {{ $prioridad_id == $prioridad->id_prioridad ? 'selected' : '' }}>
                        {{ $prioridad->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Botón Limpiar Filtros -->
<div class="row mb-4">
    <div class="col-md-12">
        <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary w-100">
            <i class="bi bi-arrow-clockwise"></i> Limpiar Filtros
        </a>
    </div>
</div>

<!-- Gráficas -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-graph-up"></i> Gráficas de Distribución
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Gráfica 1: Áreas (Circular) -->
            <div class="col-md-4">
                <h5 class="text-center mb-3">Distribución por Área</h5>
                <canvas id="chartAreas"></canvas>
            </div>
            
            <!-- Gráfica 2: Prioridades (Barras) -->
            <div class="col-md-4">
                <h5 class="text-center mb-3">Distribución por Prioridad</h5>
                <canvas id="chartPrioridades"></canvas>
            </div>
            
            <!-- Gráfica 3: Estados (Dona) -->
            <div class="col-md-4">
                <h5 class="text-center mb-3">Distribución por Estado</h5>
                <canvas id="chartEstados"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reporte por Fecha -->
<div class="modal fade" id="reporteFechaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('reportes.por-fecha') }}" method="GET">
                <div class="modal-header">
                    <h5 class="modal-title">Reporte por Rango de Fechas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" class="form-control" name="fecha_inicio" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" class="form-control" name="fecha_fin" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Generar Reporte</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Variables globales para guardar instancias de las gráficas
let chartAreaInstance = null;
let chartPrioridadesInstance = null;
let chartEstadosInstance = null;

// Colores variados y distribuidos
const coloresAreas = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8', '#F7DC6F'];
const coloresPrioridades = ['#3498DB', '#E74C3C', '#F39C12', '#9B59B6'];
const coloresEstados = ['#2ECC71', '#F39C12', '#E67E22'];

// Función para actualizar las gráficas
function actualizarGraficas() {
    // Obtener valores de los filtros
    const areaId = document.getElementById('areaFilter').value;
    const estadoId = document.getElementById('estadoFilter').value;
    const prioridadId = document.getElementById('prioridadFilter').value;
    
    // Construir URL con parámetros
    const params = new URLSearchParams();
    if (areaId) params.append('area_id', areaId);
    if (estadoId) params.append('estado_id', estadoId);
    if (prioridadId) params.append('prioridad_id', prioridadId);
    
    const url = '{{ route("reportes.index") }}?' + params.toString();
    
    // Hacer petición AJAX
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Datos recibidos:', data);
        
        // Actualizar gráfica de Áreas
        if (chartAreaInstance) {
            chartAreaInstance.data.labels = data.areas_labels;
            chartAreaInstance.data.datasets[0].data = data.areas_data;
            chartAreaInstance.update();
        }
        
        // Actualizar gráfica de Prioridades
        if (chartPrioridadesInstance) {
            chartPrioridadesInstance.data.labels = data.prioridades_labels;
            chartPrioridadesInstance.data.datasets[0].data = data.prioridades_data;
            chartPrioridadesInstance.update();
        }
        
        // Actualizar gráfica de Estados
        if (chartEstadosInstance) {
            chartEstadosInstance.data.labels = data.estados_labels;
            chartEstadosInstance.data.datasets[0].data = data.estados_data;
            chartEstadosInstance.update();
        }
    })
    .catch(error => console.error('Error:', error));
}

// Escuchar cambios en los filtros
document.getElementById('areaFilter').addEventListener('change', actualizarGraficas);
document.getElementById('estadoFilter').addEventListener('change', actualizarGraficas);
document.getElementById('prioridadFilter').addEventListener('change', actualizarGraficas);

// ============ GRÁFICA 1: ÁREAS (Circular/Pie) ============
const ctxAreas = document.getElementById('chartAreas').getContext('2d');
chartAreaInstance = new Chart(ctxAreas, {
    type: 'pie',
    data: {
        labels: {!! json_encode($areas_labels ?? []) !!},
        datasets: [{
            data: {!! json_encode($areas_data ?? []) !!},
            backgroundColor: coloresAreas,
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: { size: 11 }
                }
            },
            datalabels: {
                color: '#fff',
                font: {
                    weight: 'bold',
                    size: 12
                },
                formatter: (value, ctx) => {
                    let sum = 0;
                    let dataArr = ctx.chart.data.datasets[0].data;
                    dataArr.forEach(d => { sum += d; });
                    let percentage = (value * 100 / sum).toFixed(1) + '%';
                    return percentage;
                }
            }
        }
    },
    plugins: [ChartDataLabels]
});

// ============ GRÁFICA 2: PRIORIDADES (Línea) ============
const ctxPrioridades = document.getElementById('chartPrioridades').getContext('2d');
chartPrioridadesInstance = new Chart(ctxPrioridades, {
    type: 'line',
    data: {
        labels: {!! json_encode($prioridades_labels ?? []) !!},
        datasets: [{
            label: 'Cantidad de Tickets',
            data: {!! json_encode($prioridades_data ?? []) !!},
            borderColor: '#3498DB',
            backgroundColor: 'rgba(52, 152, 219, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 6,
            pointBackgroundColor: '#3498DB',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            },
            datalabels: {
                color: '#000',
                font: {
                    weight: 'bold',
                    size: 11
                },
                anchor: 'top',
                align: 'top',
                offset: 8,
                formatter: (value) => {
                    return value;
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    },
    plugins: [ChartDataLabels]
});

// ============ GRÁFICA 3: ESTADOS (Dona) ============
const ctxEstados = document.getElementById('chartEstados').getContext('2d');
chartEstadosInstance = new Chart(ctxEstados, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($estados_labels ?? []) !!},
        datasets: [{
            data: {!! json_encode($estados_data ?? []) !!},
            backgroundColor: coloresEstados,
            borderColor: ['#27AE60', '#D68910', '#D35400'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: { size: 11 }
                }
            },
            datalabels: {
                color: '#fff',
                font: {
                    weight: 'bold',
                    size: 12
                },
                formatter: (value, ctx) => {
                    let sum = 0;
                    let dataArr = ctx.chart.data.datasets[0].data;
                    dataArr.forEach(d => { sum += d; });
                    let percentage = (value * 100 / sum).toFixed(1) + '%';
                    return percentage;
                }
            }
        }
    },
    plugins: [ChartDataLabels]
});
</script>
@endpush
@endsection