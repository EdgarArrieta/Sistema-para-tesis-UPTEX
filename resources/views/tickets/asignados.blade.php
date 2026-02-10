@extends('layouts.app')

@section('title', 'Tickets Asignados')

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <h2><i class="bi bi-clipboard-check me-2"></i>Mis Tickets Asignados</h2>
        <p class="text-muted mb-0">Gestiona los tickets que te han sido asignados</p>
    </div>
    
    <!-- TABS DE ESTADO -->
    <div class="col-12 mb-4">
        <ul class="nav nav-pills" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-pendientes" data-bs-toggle="pill" data-bs-target="#pendientes" type="button" role="tab" aria-controls="pendientes" aria-selected="true">
                    <i class="bi bi-hourglass-split me-2"></i>Pendientes
                    <span class="badge bg-warning ms-2">{{ $stats['pendientes'] ?? 0 }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-proceso" data-bs-toggle="pill" data-bs-target="#proceso" type="button" role="tab" aria-controls="proceso" aria-selected="false">
                    <i class="bi bi-gear me-2"></i>En Proceso
                    <span class="badge bg-info ms-2">{{ $stats['en_proceso'] ?? 0 }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-resueltos" data-bs-toggle="pill" data-bs-target="#resueltos" type="button" role="tab" aria-controls="resueltos" aria-selected="false">
                    <i class="bi bi-check-circle me-2"></i>Resueltos
                    <span class="badge bg-success ms-2">{{ $stats['resueltos'] ?? 0 }}</span>
                </button>
            </li>
        </ul>
    </div>
    
    <!-- CONTENIDO TABS -->
    <div class="col-12">
        <div class="tab-content">
            <!-- PENDIENTES -->
            <div class="tab-pane fade show active" id="pendientes">
                @foreach($tickets_pendientes ?? [] as $ticket)
                <div class="card mb-3 ticket-work-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="d-flex gap-3">
                                    <div>
                                        <span class="badge bg-secondary fs-6">#{{ $ticket->id_ticket }}</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-2">{{ $ticket->titulo }}</h5>
                                        <p class="text-muted mb-3">{{ Str::limit($ticket->descripcion, 150) }}</p>
                                        
                                        <div class="d-flex gap-2 flex-wrap mb-3">
                                            <span class="badge badge-prioridad-{{ $ticket->prioridad->nivel }}">
                                                <i class="bi bi-exclamation-circle me-1"></i>
                                                {{ $ticket->prioridad->nombre }}
                                            </span>
                                            <span class="badge bg-light text-dark">
                                                <i class="bi bi-building me-1"></i>{{ $ticket->area->nombre }}
                                            </span>
                                            <span class="badge bg-light text-dark">
                                                <i class="bi bi-person me-1"></i>{{ $ticket->usuario->nombre_completo }}
                                            </span>
                                        </div>
                                        
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            Creado {{ $ticket->fecha_creacion->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 text-end">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success" onclick="iniciarTrabajo({{ $ticket->id_ticket }})">
                                        <i class="bi bi-play-circle me-1"></i>Iniciar
                                    </button>
                                    <a href="{{ route('tickets.show', $ticket->id_ticket) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>Ver
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                
                @if(count($tickets_pendientes ?? []) == 0)
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-check-circle" style="font-size: 4rem; color: #10B981;"></i>
                        <h5 class="mt-3">¡Todo al día!</h5>
                        <p class="text-muted">No tienes tickets pendientes por atender</p>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- EN PROCESO -->
            <div class="tab-pane fade" id="proceso">
                @foreach($tickets_proceso ?? [] as $ticket)
                <div class="card mb-3 ticket-work-card border-warning">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="d-flex gap-3">
                                    <div>
                                        <span class="badge bg-warning fs-6">#{{ $ticket->id_ticket }}</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-2">{{ $ticket->titulo }}</h5>
                                        <p class="text-muted mb-3">{{ Str::limit($ticket->descripcion, 150) }}</p>
                                        
                                        <div class="d-flex gap-2 flex-wrap mb-3">
                                            <span class="badge badge-prioridad-{{ $ticket->prioridad->nivel }}">
                                                {{ $ticket->prioridad->nombre }}
                                            </span>
                                            <span class="badge bg-light text-dark">
                                                {{ $ticket->area->nombre }}
                                            </span>
                                        </div>
                                        
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            En proceso desde {{ $ticket->updated_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 text-end">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#resolverModal{{ $ticket->id_ticket }}">
                                        <i class="bi bi-check-circle me-1"></i>Resolver
                                    </button>
                                    <a href="{{ route('tickets.show', $ticket->id_ticket) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>Ver
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Resolver -->
                <div class="modal fade" id="resolverModal{{ $ticket->id_ticket }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('tickets.cerrar', $ticket->id_ticket) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title">Resolver Ticket #{{ $ticket->id_ticket }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Solución Aplicada *</label>
                                        <textarea class="form-control" 
                                                  name="solucion" 
                                                  rows="5" 
                                                  placeholder="Describe detalladamente cómo resolviste el problema..."
                                                  required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle me-1"></i>Cerrar Ticket
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
                
                @if(count($tickets_proceso ?? []) == 0)
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 4rem; color: #CBD5E1;"></i>
                        <p class="text-muted mt-3">No tienes tickets en proceso</p>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- RESUELTOS -->
            <div class="tab-pane fade" id="resueltos">
                @foreach($tickets_resueltos ?? [] as $ticket)
                <div class="card mb-3 ticket-work-card border-success">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-10">
                                <div class="d-flex gap-3">
                                    <div>
                                        <span class="badge bg-success fs-6">#{{ $ticket->id_ticket }}</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-2">{{ $ticket->titulo }}</h5>
                                        <p class="text-muted mb-2">
                                            <strong>Solución:</strong> {{ Str::limit($ticket->solucion, 120) }}
                                        </p>
                                        <small class="text-muted">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Resuelto {{ $ticket->fecha_cierre->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-2 text-end">
                                <a href="{{ route('tickets.show', $ticket->id_ticket) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                
                @if(count($tickets_resueltos ?? []) == 0)
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 4rem; color: #CBD5E1;"></i>
                        <p class="text-muted mt-3">No has resuelto tickets recientemente</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function iniciarTrabajo(ticketId) {
    if (!confirm('¿Comenzar a trabajar en este ticket?')) return;
    
    const btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Iniciando...';
    
    // Cambiar estado a "En Proceso" (estado_id = 2)
    fetch(`/tickets/${ticketId}/cambiar-estado`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            estado_id: 2
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar alerta de éxito
            showNotification('Ticket iniciado correctamente', 'success');
            // Recargar solo después de 500ms para mejor UX
            setTimeout(() => location.reload(), 500);
        } else {
            showNotification('Error: ' + (data.message || 'No se pudo iniciar'), 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-play-circle me-1"></i>Iniciar';
        }
    })
    .catch(error => {
        showNotification('Error al conectar con el servidor', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-play-circle me-1"></i>Iniciar';
    });
}

// Notificación simple
function showNotification(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.insertBefore(alertDiv, document.body.firstChild);
    setTimeout(() => alertDiv.remove(), 3000);
}
</script>
@endpush

@push('styles')
<style>
    .ticket-work-card {
        transition: all 0.2s ease;
    }
    
    .ticket-work-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }
    
    .nav-pills {
        gap: 0.5rem;
    }
    
    .nav-pills .nav-link {
        border-radius: 8px;
        font-weight: 500;
        background: #f3f4f6;
        color: #64748b;
        transition: all 0.2s ease;
    }
    
    .nav-pills .nav-link:hover {
        background: #e2e8f0;
        color: #475569;
    }
    
    .nav-pills .nav-link.active {
        background: #4F46E5;
        color: white;
    }
    
    .tab-pane {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush
@endsection