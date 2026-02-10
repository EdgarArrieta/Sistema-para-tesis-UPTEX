@extends('layouts.app')

@section('title', 'Historial de Tickets')

@section('content')
<div class="row">
    <!-- HEADER -->
    <div class="col-12 mb-4">
        <h2><i class="bi bi-clock-history me-2"></i>Historial de Tickets</h2>
        <p class="text-muted">Todos los tickets que has atendido</p>
    </div>
    
    <!-- LISTA DE TICKETS -->
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if($tickets->count() > 0)
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
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                            <tr>
                                <td><strong>#{{ $ticket->id_ticket }}</strong></td>
                                <td>{{ Str::limit($ticket->titulo, 50) }}</td>
                                <td>{{ $ticket->usuario->nombre_completo }}</td>
                                <td>{{ $ticket->area->nombre }}</td>
                                <td>
                                    <span class="badge badge-prioridad-{{ $ticket->prioridad->nivel }}">
                                        {{ $ticket->prioridad->nombre }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-estado-{{ $ticket->estado->tipo }}">
                                        {{ $ticket->estado->nombre }}
                                    </span>
                                </td>
                                <td>{{ $ticket->fecha_creacion->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('tickets.show', $ticket->id_ticket) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- PAGINACIÓN -->
                @if($tickets->hasPages())
                <div class="mt-4">
                    {{ $tickets->links() }}
                </div>
                @endif
                @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: #CBD5E1;"></i>
                    <p class="text-muted mt-3">No tienes tickets en tu historial</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
