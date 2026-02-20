@extends('layouts.app')

@section('title', 'Mis Tickets')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-ticket-perforated me-2"></i>Mis Tickets</h2>
                <p class="text-muted mb-0">Todos tus tickets de soporte</p>
            </div>
            @if(!str_contains(session('usuario_rol'), 'Técnico'))
            <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Nuevo Ticket
            </a>
            @endif
        </div>
    </div>
    
    <!-- FILTROS -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('tickets.mis-tickets') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <select name="estado_id" class="form-select">
                            <option value="">Todos los estados</option>
                            @foreach($estados ?? [] as $estado)
                            <option value="{{ $estado['id_estado'] }}" {{ request('estado_id') == $estado['id_estado'] ? 'selected' : '' }}>
                                {{ $estado['nombre'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <select name="prioridad_id" class="form-select">
                            <option value="">Todas las prioridades</option>
                            @foreach($prioridades ?? [] as $prioridad)
                            <option value="{{ $prioridad['id_prioridad'] }}" {{ request('prioridad_id') == $prioridad['id_prioridad'] ? 'selected' : '' }}>
                                {{ $prioridad['nombre'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Buscar en mis tickets..." value="{{ request('search') }}">
                    </div>
                    
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- LISTA DE TICKETS -->
    <div class="col-12">
        @if(str_contains(session('usuario_rol'), 'Técnico'))
        <!-- VISTA DE TABLA PARA TÉCNICO -->
        @if(count($tickets) > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Folio</th>
                        <th>Título del Ticket</th>
                        <th>Prioridad</th>
                        <th>Estado Actual</th>
                        <th>Fecha de Creación</th>
                        <th>Fecha de Cierre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                    <tr>
                        <td><strong>#{{ $ticket->id_ticket }}</strong></td>
                        <td>
                            <a href="{{ route('tickets.show', $ticket->id_ticket) }}" class="text-decoration-none">
                                {{ $ticket->titulo }}
                            </a>
                            <br>
                            <small class="text-muted">{{ $ticket->usuario->nombre ?? 'N/A' }} {{ $ticket->usuario->apellido ?? '' }}</small>
                        </td>
                        <td>
                            <span class="badge badge-prioridad-{{ $ticket->prioridad->nivel ?? 'media' }}">
                                {{ $ticket->prioridad->nombre ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-estado-{{ $ticket->estado->tipo ?? 'abierto' }}">
                                {{ $ticket->estado->nombre ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <small>{{ $ticket->fecha_creacion ? $ticket->fecha_creacion->format('d/m/Y H:i') : 'N/A' }}</small>
                        </td>
                        <td>
                            <small>{{ $ticket->fecha_cierre ? $ticket->fecha_cierre->format('d/m/Y H:i') : '--' }}</small>
                        </td>
                        <td>
                            <a href="{{ route('tickets.show', $ticket->id_ticket) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-eye"></i> Gestionar
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 4rem; color: #CBD5E1;"></i>
                <h5 class="mt-3">No tienes tickets aún</h5>
                <p class="text-muted">El administrador aun no te ha asignado tickets para resolver</p>
            </div>
        </div>
        @endif

        @else
        <!-- VISTA DE TARJETAS PARA USUARIO NORMAL -->
        @forelse($tickets as $ticket)
        <div class="card mb-3 ticket-card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-start gap-3">
                            <div class="ticket-id">
                                <span class="badge bg-secondary">#{{ $ticket->id_ticket }}</span>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-2">
                                    <a href="{{ route('tickets.show', $ticket->id_ticket) }}" class="text-decoration-none text-dark">
                                        {{ $ticket->titulo }}
                                    </a>
                                </h5>
                                <p class="text-muted mb-2">{{ Str::limit($ticket->descripcion, 120) }}</p>
                                <div class="d-flex gap-2 flex-wrap">
                                    <span class="badge badge-estado-{{ $ticket->estado->tipo }}">
                                        {{ $ticket->estado->nombre }}
                                    </span>
                                    <span class="badge badge-prioridad-{{ $ticket->prioridad->nivel }}">
                                        {{ $ticket->prioridad->nombre }}
                                    </span>
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-building me-1"></i>{{ $ticket->area->nombre }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <p class="text-muted small mb-2">
                            <i class="bi bi-clock me-1"></i>
                            {{ $ticket->fecha_creacion->diffForHumans() }}
                        </p>
                        @if($ticket->tecnicoAsignado)
                        <p class="text-muted small mb-2">
                            <i class="bi bi-person-badge me-1"></i>
                            {{ $ticket->tecnicoAsignado->nombre_completo }}
                        </p>
                        @endif
                        <a href="{{ route('tickets.show', $ticket->id_ticket) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-eye me-1"></i>Ver Detalles
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 4rem; color: #CBD5E1;"></i>
                <h5 class="mt-3">No tienes tickets aún</h5>
                <p class="text-muted">Crea tu primer ticket para recibir soporte</p>
                @if(!str_contains(session('usuario_rol'), 'Técnico'))
                <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Crear Ticket
                </a>
                @endif
            </div>
        </div>
        @endforelse
        @endif
    </div>
</div>

@push('styles')
<style>
    .ticket-card {
        transition: all 0.2s ease;
    }
    
    .ticket-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .ticket-id {
        font-size: 1.25rem;
        font-weight: 700;
    }
</style>
@endpush
@endsection