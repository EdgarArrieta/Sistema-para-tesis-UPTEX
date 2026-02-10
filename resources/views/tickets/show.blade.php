@extends('layouts.app')

@section('title', 'Ticket #' . $ticket['id_ticket'])

@section('content')
<div class="row">
    <!-- HEADER -->
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2>
                    <span class="badge bg-secondary me-2">#{{ $ticket['id_ticket'] }}</span>
                    {{ $ticket['titulo'] }}
                </h2>
                <div class="mt-2">
                    <span class="badge badge-estado-{{ $ticket['estado']['tipo'] ?? 'abierto' }} fs-6">
                        {{ $ticket['estado']['nombre'] ?? 'N/A' }}
                    </span>
                    <span class="badge badge-prioridad-{{ $ticket['prioridad']['nivel'] ?? 1 }} fs-6 ms-2">
                        {{ $ticket['prioridad']['nombre'] ?? 'N/A' }}
                    </span>
                </div>
            </div>
            <div>
                @if(session('usuario_rol') != 'Usuario Normal')
                <a href="{{ route('tickets.edit', $ticket['id_ticket']) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                @endif
                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
    
    <!-- CONTENIDO PRINCIPAL -->
    <div class="col-lg-8">
        <!-- INFORMACIÓN DEL TICKET -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Detalles del Ticket</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p class="text-muted mb-1"><i class="bi bi-person me-2"></i>Creado por</p>
                        <p class="fw-semibold">{{ $ticket['usuario']['nombre_completo'] ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1"><i class="bi bi-building me-2"></i>Área</p>
                        <p class="fw-semibold">{{ $ticket['area']['nombre'] ?? 'N/A' }}</p>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p class="text-muted mb-1"><i class="bi bi-calendar me-2"></i>Fecha de creación</p>
                        <p class="fw-semibold">{{ \Carbon\Carbon::parse($ticket['fecha_creacion'])->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted mb-1"><i class="bi bi-person-badge me-2"></i>Técnico asignado</p>
                        <p class="fw-semibold">
                            @if(isset($ticket['tecnico_asignado']))
                                {{ $ticket['tecnico_asignado']['nombre_completo'] }}
                            @else
                                <span class="text-muted">Sin asignar</span>
                            @endif
                        </p>
                    </div>
                </div>
                
                <hr>
                
                <h6 class="fw-semibold mb-3">Descripción:</h6>
                <p class="text-muted">{{ $ticket['descripcion'] }}</p>
                
                @if(isset($ticket['solucion']))
                <hr>
                <div class="alert alert-success">
                    <h6 class="fw-semibold"><i class="bi bi-check-circle me-2"></i>Solución Aplicada:</h6>
                    <p class="mb-0">{{ $ticket['solucion'] }}</p>
                    @if(isset($ticket['fecha_cierre']))
                    <small class="text-muted d-block mt-2">
                        Cerrado el {{ \Carbon\Carbon::parse($ticket['fecha_cierre'])->format('d/m/Y H:i') }}
                    </small>
                    @endif
                </div>
                @endif
            </div>
        </div>
        
        <!-- COMENTARIOS -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-chat-dots me-2"></i>
                    Comentarios ({{ count($comentarios ?? []) }})
                </h5>
            </div>
            <div class="card-body">
                <div class="comments-list mb-4">
                    @forelse($comentarios ?? [] as $comentario)
                    <div class="comment-item {{ $comentario['usuario']['id_usuario'] == session('usuario_id') ? 'own-comment' : '' }}">
                        <div class="d-flex gap-3 mb-3">
                            <div class="comment-avatar">
                                {{ strtoupper(substr($comentario['usuario']['nombre_completo'] ?? 'U', 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="comment-header mb-2">
                                    <strong>{{ $comentario['usuario']['nombre_completo'] ?? 'Usuario' }}</strong>
                                    <span class="text-muted ms-2">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($comentario['created_at'])->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="comment-body">
                                    {{ $comentario['contenido'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center py-4">No hay comentarios aún</p>
                    @endforelse
                </div>
                
                <hr>
                
                <h6 class="fw-semibold mb-3">Agregar comentario:</h6>
                <form action="{{ route('tickets.comentarios.store', $ticket['id_ticket']) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <textarea class="form-control" 
                                  name="contenido" 
                                  rows="3" 
                                  placeholder="Escribe tu comentario aquí..."
                                  required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-2"></i>Enviar Comentario
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- SIDEBAR ACCIONES -->
    <div class="col-lg-4">
        @if(session('usuario_rol') == 'Administrador' || session('usuario_rol') == 'Técnico')
        <div class="card mb-3 sticky-top" style="top: 90px;">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Acciones</h5>
            </div>
            <div class="card-body">
                @if(session('usuario_rol') == 'Administrador')
                <!-- Asignar técnico -->
                <form action="{{ route('tickets.asignar', $ticket['id_ticket']) }}" method="POST" class="mb-3">
                    @csrf
                    <label class="form-label fw-semibold">Asignar técnico:</label>
                    <div class="input-group">
                        <select name="tecnico_id" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            @foreach($tecnicos ?? [] as $tecnico)
                            <option value="{{ $tecnico['id_usuario'] }}" 
                                    {{ isset($ticket['tecnico_asignado']) && $ticket['tecnico_asignado']['id'] == $tecnico['id_usuario'] ? 'selected' : '' }}>
                                {{ $tecnico['nombre_completo'] }}
                            </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i>
                        </button>
                    </div>
                </form>
                @endif
                
                <!-- Cambiar estado -->
                <form action="{{ route('tickets.cambiar-estado', $ticket['id_ticket']) }}" method="POST" class="mb-3">
                    @csrf
                    <label class="form-label fw-semibold">Cambiar estado:</label>
                    <div class="input-group">
                        <select name="estado_id" class="form-select" required>
                            @foreach($estados ?? [] as $estado)
                            <option value="{{ $estado['id_estado'] }}" {{ $ticket['estado']['id_estado'] == $estado['id_estado'] ? 'selected' : '' }}>
                                {{ $estado['nombre'] }}
                            </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check"></i>
                        </button>
                    </div>
                </form>
                
                <!-- Cerrar ticket -->
                @if($ticket['estado']['tipo'] != 'cerrado')
                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#cerrarModal">
                    <i class="bi bi-check-circle me-2"></i>Actualizar Estado del Ticket
                </button>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Información adicional -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-1">Última actualización:</p>
                <p class="fw-semibold mb-3">{{ \Carbon\Carbon::parse($ticket['updated_at'])->diffForHumans() }}</p>
                
                @if(isset($ticket['fecha_cierre']))
                <p class="text-muted mb-1">Fecha de cierre:</p>
                <p class="fw-semibold">{{ \Carbon\Carbon::parse($ticket['fecha_cierre'])->format('d/m/Y H:i') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Cerrar Ticket -->
<div class="modal fade" id="cerrarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('tickets.cerrar', $ticket['id_ticket']) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Cerrar Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Asignar técnico -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Asignar técnico:</label>
                        <select class="form-select" name="tecnico_asignado_id">
                            <option value="">Sin asignar</option>
                            @foreach($tecnicos ?? [] as $tecnico)
                            <option value="{{ $tecnico['id_usuario'] }}" 
                                    {{ isset($ticket['tecnico_asignado']) && $ticket['tecnico_asignado']['id'] == $tecnico['id_usuario'] ? 'selected' : '' }}>
                                {{ $tecnico['nombre_completo'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Cambiar estado -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cambiar estado:</label>
                        <select class="form-select" name="estado_id" required>
                            @foreach($estados ?? [] as $estado)
                            <option value="{{ $estado['id_estado'] }}" {{ $ticket['estado']['id_estado'] == $estado['id_estado'] ? 'selected' : '' }}>
                                {{ $estado['nombre'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Solución -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Solución Aplicada: *</label>
                        <textarea class="form-control" 
                                  name="solucion" 
                                  rows="5" 
                                  placeholder="Describe detalladamente cómo se resolvió el problema..."
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-2"></i>Cerrar Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .comment-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4F46E5, #7C3AED);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        flex-shrink: 0;
    }
    
    .comment-item {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        background: #F8FAFC;
    }
    
    .own-comment {
        background: #EEF2FF;
        border-left: 3px solid #4F46E5;
    }
    
    .comment-header {
        font-size: 0.9rem;
    }
    
    .comment-body {
        color: #334155;
        line-height: 1.6;
    }
</style>
@endpush

@push('scripts')
<script>
function asignarTecnico() {
    const tecnicoId = document.getElementById('tecnicoSelect').value;
    if (!tecnicoId) {
        alert('Selecciona un técnico');
        return;
    }
    
    fetch('{{ route("tickets.asignar", $ticket["id_ticket"]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            tecnico_id: tecnicoId
        })
    })
    .then(response => {
        if (response.ok) {
            location.reload();
        } else {
            alert('Error al asignar técnico');
        }
    })
    .catch(error => console.error('Error:', error));
}

function cambiarEstado() {
    const estadoId = document.getElementById('estadoSelect').value;
    if (!estadoId) {
        alert('Selecciona un estado');
        return;
    }
    
    console.log('Cambiar estado a:', estadoId);
    
    fetch('{{ route("tickets.cambiar-estado", $ticket["id_ticket"]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            estado_id: estadoId
        })
    })
    .then(response => {
        console.log('Respuesta cambiar estado:', response.status);
        if (response.ok) {
            location.reload();
        } else {
            alert('Error al cambiar estado');
        }
    })
    .catch(error => console.error('Error:', error));
}

function cerrarTicket() {
    // Obtener el estado "Cerrado"
    const estadoSelect = document.getElementById('estadoSelect');
    const cerradoOption = Array.from(estadoSelect.options).find(opt => 
        opt.textContent.toLowerCase().includes('cerrado')
    );
    
    if (!cerradoOption) {
        alert('No se encontró estado Cerrado');
        return;
    }
    
    // Cambiar el select al estado cerrado antes de enviar
    estadoSelect.value = cerradoOption.value;
    
    fetch('{{ route("tickets.cerrar", $ticket["id_ticket"]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            estado_id: cerradoOption.value,
            solucion: 'Ticket cerrado desde gestor de tickets'
        })
    })
    .then(response => {
        if (response.ok) {
            // Redirigir a la tabla de tickets después de cerrar
            window.location.href = '{{ route("tickets.index") }}';
        } else {
            alert('Error al cerrar ticket');
        }
    })
    .catch(error => console.error('Error:', error));
@endpush
@endsection