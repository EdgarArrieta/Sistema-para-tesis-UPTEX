@extends('layouts.app')

@section('title', 'Tickets - Sistema de Tickets')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-ticket"></i> Gestión de Tickets</h2>
    
    {{-- OCULTAR BOTÓN SUPERIOR PARA TÉCNICOS --}}
    @if(!str_contains(session('usuario_rol'), 'Técnico'))
    <a href="{{ route('tickets.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nuevo Ticket
    </a>
    @endif
</div>

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
                                <a href="{{ route('tickets.show', $ticket['id_ticket']) }}" class="btn btn-info" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(str_contains(session('usuario_rol'), 'Administrador') || str_contains(session('usuario_rol'), 'Técnico'))
                                <a href="{{ route('tickets.edit', $ticket['id_ticket']) }}" class="btn btn-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-2">No tienes tickets asignados para mostrar</p>
                            
                            {{-- OCULTAR BOTÓN CENTRAL PARA TÉCNICOS --}}
                            @if(!str_contains(session('usuario_rol'), 'Técnico'))
                            <a href="{{ route('tickets.create') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle"></i> Crear Ticket
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection