@extends('layouts.app')

@section('title', 'Tickets - Sistema de Tickets')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-ticket"></i> Gestión de Tickets</h2>
    <a href="{{ route('tickets.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Ticket
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select name="estado_id" id="estadoFilter" class="form-select">
                    <option value="">Todos</option>
                    @foreach($estados ?? [] as $estado)
                    <option value="{{ $estado['id_estado'] }}" {{ request('estado_id') == $estado['id_estado'] ? 'selected' : '' }}>
                        {{ $estado['nombre'] }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Prioridad</label>
                <select name="prioridad_id" id="prioridadFilter" class="form-select">
                    <option value="">Todas</option>
                    @foreach($prioridades ?? [] as $prioridad)
                    <option value="{{ $prioridad['id_prioridad'] }}" {{ request('prioridad_id') == $prioridad['id_prioridad'] ? 'selected' : '' }}>
                        {{ $prioridad['nombre'] }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Área</label>
                <select name="area_id" id="areaFilter" class="form-select">
                    <option value="">Todas</option>
                    @foreach($areas ?? [] as $area)
                    <option value="{{ $area['id_area'] }}" {{ request('area_id') == $area['id_area'] ? 'selected' : '' }}>
                        {{ $area['nombre'] }}
                    </option>
                    @endforeach
                </select>
            </div>
            

        </div>
    </div>
</div>

<!-- Tabla de Tickets -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="ticketsTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Usuario</th>
                        <th>Área</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Técnico</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td><strong>#{{ $ticket['id_ticket'] }}</strong></td>
                        <td>
                            <a href="{{ route('tickets.show', $ticket['id_ticket']) }}" class="text-decoration-none">
                                {{ Str::limit($ticket['titulo'], 50) }}
                            </a>
                        </td>
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
                        <td>
                            @if(isset($ticket['tecnico_asignado']))
                                {{ $ticket['tecnico_asignado']['nombre_completo'] }}
                            @else
                                <span class="text-muted">Sin asignar</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($ticket['fecha_creacion'])->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('tickets.show', $ticket['id_ticket']) }}" 
                                   class="btn btn-info" 
                                   title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                @if(session('usuario_rol') == 'Administrador' || session('usuario_rol') == 'Técnico')
                                <a href="{{ route('tickets.edit', $ticket['id_ticket']) }}" 
                                   class="btn btn-warning" 
                                   title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                                
                                @if(session('usuario_rol') == 'Administrador')
                                <form action="{{ route('tickets.destroy', $ticket['id_ticket']) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('¿Estás seguro de eliminar este ticket?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-2">No hay tickets para mostrar</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if(isset($pagination) && $pagination['total'] > 0)
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                Total: {{ $pagination['total'] }} tickets
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    let dataTableInstance = null;
    
    // Función para cargar y actualizar tickets
    function cargarTickets() {
        const estadoId = $('#estadoFilter').val();
        const prioridadId = $('#prioridadFilter').val();
        const areaId = $('#areaFilter').val();
        const search = $('#searchFilter').val();
        
        // Construir URL con parámetros
        const params = new URLSearchParams();
        if (estadoId) params.append('estado_id', estadoId);
        if (prioridadId) params.append('prioridad_id', prioridadId);
        if (areaId) params.append('area_id', areaId);
        if (search) params.append('search', search);
        
        const url = '{{ route("tickets.index") }}?' + params.toString();
        
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
            // Destruir DataTable anterior si existe
            if (dataTableInstance) {
                dataTableInstance.destroy();
            }
            
            // Limpiar tabla
            let tbody = $('#ticketsTable tbody');
            tbody.empty();
            
            // Agregar filas
            if (data.tickets && data.tickets.length > 0) {
                data.tickets.forEach(ticket => {
                    const row = `
                        <tr>
                            <td><strong>#${ticket.id_ticket}</strong></td>
                            <td>
                                <a href="/tickets/${ticket.id_ticket}" class="text-decoration-none">
                                    ${ticket.titulo.substring(0, 50)}
                                </a>
                            </td>
                            <td>${ticket.usuario.nombre_completo || 'N/A'}</td>
                            <td>${ticket.area.nombre || 'N/A'}</td>
                            <td>
                                <span class="badge badge-prioridad-${ticket.prioridad.nivel || 1}">
                                    ${ticket.prioridad.nombre || 'N/A'}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-${getEstadoBadgeClass(ticket.estado.tipo)}">
                                    ${ticket.estado.nombre || 'N/A'}
                                </span>
                            </td>
                            <td>${ticket.tecnico_asignado ? ticket.tecnico_asignado.nombre_completo : 'Sin asignar'}</td>
                            <td>${new Date(ticket.fecha_creacion).toLocaleDateString('es-ES', {year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit'})}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/tickets/${ticket.id_ticket}" class="btn btn-info" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="/tickets/${ticket.id_ticket}/edit" class="btn btn-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            } else {
                tbody.append(`
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-2">No hay tickets para mostrar</p>
                        </td>
                    </tr>
                `);
            }
            
            // Reinicializar DataTable
            dataTableInstance = $('#ticketsTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
                },
                "pageLength": 15,
                "order": [[0, 'desc']],
                "columnDefs": [
                    { "orderable": false, "targets": 8 }
                ],
                "dom": "lrtip"
            });
        })
        .catch(error => console.error('Error:', error));
    }
    
    // Función para obtener clase de badge según estado
    function getEstadoBadgeClass(tipo) {
        switch(tipo) {
            case 'abierto': return 'primary';
            case 'en_proceso': return 'warning';
            case 'cerrado': return 'success';
            default: return 'secondary';
        }
    }
    
    // Event listeners para los filtros
    $('#estadoFilter').on('change', cargarTickets);
    $('#prioridadFilter').on('change', cargarTickets);
    $('#areaFilter').on('change', cargarTickets);
    $('#btnBuscar').on('click', cargarTickets);
    
    // También permitir Enter en el campo de búsqueda
    $('#searchFilter').on('keypress', function(e) {
        if (e.which == 13) { // Enter key
            cargarTickets();
            return false;
        }
    });
    
    // Inicializar DataTable
    if ($('#ticketsTable tbody tr').length > 0) {
        dataTableInstance = $('#ticketsTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
            },
            "pageLength": 15,
            "order": [[0, 'desc']],
            "columnDefs": [
                { "orderable": false, "targets": 8 }
            ],
            "dom": "lrtip"
        });
    }
});
</script>
@endpush
@endsection