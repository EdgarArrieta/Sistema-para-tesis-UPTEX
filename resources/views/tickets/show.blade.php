@extends('layouts.app')

@section('title', 'Detalle del Ticket #' . $ticket['id_ticket'])

@section('content')

{{-- CSS PERSONALIZADO PARA DIFERENCIAR ROLES --}}
<style>
    .bg-brown { background-color: #795548 !important; }
    .btn-brown { background-color: #795548; border-color: #795548; color: white; }
    .btn-brown:hover { background-color: #5d4037; border-color: #5d4037; color: white; }
    .border-brown { border-color: #795548 !important; }
    .text-brown { color: #795548 !important; }
    .modal-xl { max-width: 90%; }
    .shadow-inset { box-shadow: inset 0 2px 4px rgba(0,0,0,.06); }
    @media (max-width: 768px) {
        .modal-xl { max-width: 95%; }
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <span class="badge bg-secondary mb-2 fs-6">Ticket #{{ $ticket['id_ticket'] }}</span>
        <h2 class="mb-0 fw-bold">{{ $ticket['titulo'] }}</h2>
    </div>
    <div class="btn-group shadow-sm">
        <a href="{{ route('tickets.edit', $ticket['id_ticket']) }}" class="btn btn-warning fw-bold">
            <i class="bi bi-pencil-square me-1"></i> Editar Gestión
        </a>
        <a href="{{ route('tickets.asignados') }}" class="btn btn-secondary fw-bold">
            <i class="bi bi-arrow-left-circle me-1"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title mb-0 fw-bold text-primary">
                    <i class="bi bi-info-circle-fill me-2"></i>Detalles del Reporte
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase">Creado por:</label>
                        <p class="fw-bold fs-5">{{ $ticket['usuario']['nombre_completo'] }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase">Área:</label>
                        <p class="fw-bold fs-5">{{ $ticket['area']['nombre'] }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase">Fecha de creación:</label>
                        <p class="fw-bold fs-5">{{ \Carbon\Carbon::parse($ticket['fecha_creacion'])->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold text-uppercase text-primary">Técnico asignado:</label>
                        <p class="fw-bold fs-5 text-primary">{{ $ticket['tecnico_asignado']['nombre_completo'] ?? 'Sin asignar' }}</p>
                    </div>
                </div>
                <hr>
                <div class="mt-3">
                    <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Descripción del problema:</label>
                    <div class="p-3 bg-light rounded border">
                        <p class="fs-5 mb-0">{{ $ticket['descripcion'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-gear-fill me-2"></i>Acciones</h5>
            </div>
            <div class="card-body text-center d-flex flex-column justify-content-center">
                @php $esTecnico = (session('usuario_rol') == 'Técnico'); @endphp
                
                <div class="mb-4">
                    <p class="text-muted small mb-2 text-uppercase fw-bold">Estado Actual:</p>
                    <div class="border rounded py-3 px-4 d-inline-block fw-bold bg-white shadow-sm fs-4 {{ $esTecnico ? 'text-success' : 'text-brown' }}">
                        {{ $ticket['estado']['nombre'] }}
                    </div>
                </div>

                {{-- BOTÓN DINÁMICO SEGÚN ROL --}}
                @if($esTecnico)
                    <button type="button" class="btn btn-success w-100 py-3 fw-bold fs-5 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalGestionTicket">
                        <i class="bi bi-check-circle-fill me-2"></i>Actualizar Estado (Técnico)
                    </button>
                @else
                    <button type="button" class="btn btn-brown w-100 py-3 fw-bold fs-5 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalGestionTicket">
                        <i class="bi bi-shield-lock-fill me-2"></i>Modificar Estado (Admin)
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalGestionTicket" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg">
            {{-- CABECERA DINÁMICA: VERDE O MARRÓN --}}
            <div class="modal-header {{ $esTecnico ? 'bg-success' : 'bg-brown' }} text-white py-3">
                <h4 class="modal-title fw-bold">
                    <i class="bi {{ $esTecnico ? 'bi-sliders' : 'bi-shield-shaded' }} me-2"></i>
                    Gestión de Ticket - Modo {{ session('usuario_rol') }}
                </h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ route('tickets.cambiar-estado', $ticket['id_ticket']) }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <div class="row">
                        <div class="col-md-5 border-end">
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Usuario operando:</label>
                                <p class="fs-4 mb-0 fw-bold">
                                    <i class="bi bi-person-circle me-2 {{ $esTecnico ? 'text-success' : 'text-brown' }}"></i>
                                    {{ session('usuario_nombre') }}
                                </p>
                                <input type="hidden" name="tecnico_id" value="{{ auth()->id() }}">
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Cambiar estado a:</label>
                                <select name="estado_id" class="form-select form-select-lg fw-bold {{ $esTecnico ? 'border-success text-success' : 'border-brown text-brown shadow-sm' }}">
                                    @foreach($estados ?? [] as $est)
                                        @if($esTecnico)
                                            {{-- FILTRO PARA TÉCNICO: EN PROCESO, PENDIENTE, RESUELTO --}}
                                            @if(in_array($est['nombre'], ['En Proceso', 'Pendiente', 'Resuelto']))
                                                <option value="{{ $est['id_estado'] }}" {{ $ticket['estado']['id_estado'] == $est['id_estado'] ? 'selected' : '' }}>
                                                    {{ $est['nombre'] }}
                                                </option>
                                            @endif
                                        @else
                                            {{-- FILTRO PARA ADMINISTRADOR: ABIERTO Y CERRADO --}}
                                            @if(in_array($est['nombre'], ['Abierto', 'Cerrado']))
                                                <option value="{{ $est['id_estado'] }}" {{ $ticket['estado']['id_estado'] == $est['id_estado'] ? 'selected' : '' }}>
                                                    {{ $est['nombre'] }}
                                                </option>
                                            @endif
                                        @endif
                                    @endforeach
                                </select>
                                <small class="text-muted mt-2 d-block">
                                    <i class="bi bi-info-circle me-1"></i>
                                    {{ $esTecnico ? 'Opciones limitadas para técnico.' : 'Opciones exclusivas para administrador.' }}
                                </small>
                            </div>
                        </div>

                        <div class="col-md-7 ps-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-2">Historial de Comentarios:</label>
                            <div class="bg-light p-3 rounded border mb-3 shadow-inset" style="max-height: 250px; overflow-y: auto;">
                                @forelse($comentarios ?? [] as $comentario)
                                    <div class="mb-2 p-2 bg-white rounded border-start border-4 {{ $esTecnico ? 'border-success' : 'border-brown' }} shadow-sm">
                                        <div class="d-flex justify-content-between mb-1 border-bottom pb-1">
                                            <span class="fw-bold">{{ $comentario['usuario']['nombre_completo'] }}</span>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($comentario['created_at'])->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-0 text-secondary">{{ $comentario['contenido'] }}</p>
                                    </div>
                                @empty
                                    <p class="text-center text-muted py-3 mb-0">No hay comentarios previos.</p>
                                @endforelse
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">Agregar comentario / avance: *</label>
                                <textarea class="form-control {{ $esTecnico ? 'border-success' : 'border-brown shadow-sm' }}" name="contenido" rows="4" placeholder="Indica el motivo del cambio de estado..." required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-3 border-top">
                    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn {{ $esTecnico ? 'btn-success' : 'btn-brown' }} px-5 fw-bold shadow">
                        <i class="bi bi-save me-1"></i> {{ $esTecnico ? 'Guardar Cambios' : 'Aplicar Cambios (Admin)' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection