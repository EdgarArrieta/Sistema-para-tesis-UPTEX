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

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <span class="badge bg-secondary mb-2 fs-6">Ticket #{{ $ticket['id_ticket'] }}</span>
        <h2 class="mb-0 fw-bold">{{ $ticket['titulo'] }}</h2>
    </div>
    <div class="btn-group shadow-sm">
        @if(!str_contains(session('usuario_rol'), 'Técnico'))
        <a href="{{ route('tickets.edit', $ticket['id_ticket']) }}" class="btn btn-warning fw-bold">
            <i class="bi bi-pencil-square me-1"></i> Editar Gestión
        </a>
        @endif
        <a href="{{ route('tickets.asignados') }}" class="btn btn-secondary fw-bold">
            <i class="bi bi-arrow-left-circle me-1"></i> Volver
        </a>
    </div>
</div>

@php $esTecnico = str_contains(session('usuario_rol'), 'Técnico'); @endphp

<div class="row g-2" style="min-height: calc(100vh - 250px)">
    <div class="{{ $esTecnico ? 'col-lg-9 col-12' : 'col-lg-8 col-12' }}">
        <div class="card border-0 shadow-sm h-100 d-flex flex-column">
            <div class="card-header bg-white py-2 border-bottom">
                <h5 class="card-title mb-0 fw-bold text-primary small">
                    <i class="bi bi-info-circle-fill me-2"></i>Detalle del Problema
                </h5>
            </div>
            <div class="card-body d-flex flex-column">
                <div class="row g-2 mb-2">
                    <div class="{{ $esTecnico ? 'col-md-4 col-6' : 'col-md-6 col-6' }}">
                        <label class="text-muted fw-bold text-uppercase" style="font-size: 0.95rem;">Creado por:</label>
                        <p class="fw-bold fs-5 mb-0">{{ $ticket['usuario']['nombre_completo'] }}</p>
                    </div>
                    <div class="{{ $esTecnico ? 'col-md-4 col-6' : 'col-md-6 col-6' }}">
                        <label class="text-muted fw-bold text-uppercase" style="font-size: 0.95rem;">Departamento:</label>
                        <p class="fw-bold fs-5 mb-0">{{ $ticket['area']['nombre'] }}</p>
                    </div>
                    <div class="{{ $esTecnico ? 'col-md-4 col-6' : 'col-md-6 col-6' }}">
                        <label class="text-muted fw-bold text-uppercase" style="font-size: 0.95rem;">Fecha de creación:</label>
                        <p class="fw-bold fs-5 mb-0">{{ \Carbon\Carbon::parse($ticket['fecha_creacion'])->format('d/m/Y H:i') }}</p>
                    </div>
                    @if(!$esTecnico)
                    <div class="col-md-6 col-6">
                        <label class="text-muted fw-bold text-uppercase text-primary" style="font-size: 0.95rem;">Técnico asignado:</label>
                        <p class="fw-bold fs-5 mb-0 text-primary">{{ $ticket['tecnico_asignado']['nombre_completo'] ?? 'Sin asignar' }}</p>
                    </div>
                    @endif
                    @if($esTecnico)
                    <div class="col-md-4 col-6">
                        <label class="text-muted fw-bold text-uppercase" style="font-size: 0.95rem;">Prioridad:</label>
                        <p class="fw-bold fs-5 mb-0">
                            <span class="badge badge-prioridad-{{ $ticket['prioridad']['nivel'] ?? 'media' }}">
                                {{ $ticket['prioridad']['nombre'] ?? 'N/A' }}
                            </span>
                        </p>
                    </div>
                    @endif
                </div>
                <hr class="my-2">
                <div class="mb-2">
                    <label class="text-muted fw-bold text-uppercase mb-1 d-block" style="font-size: 0.95rem;">Descripción del problema:</label>
                    <div class="p-2 bg-light rounded border" style="max-height: 120px; overflow-y: auto;">
                        <p class="fs-5 mb-0">{{ $ticket['descripcion'] }}</p>
                    </div>
                </div>
                
                @if($esTecnico)
                <hr class="my-2">
                <div class="flex-grow-1 d-flex flex-column">
                    <label class="text-muted fw-bold text-uppercase mb-1 d-block" style="font-size: 1rem;">Historial de Comentarios:</label>
                    <div class="bg-light p-2 rounded border shadow-inset flex-grow-1" style="overflow-y: auto;" data-comentarios-container>
                        @forelse($comentarios ?? [] as $comentario)
                            <div class="mb-2 p-2 bg-white rounded border-start border-4 border-success shadow-sm">
                                <div class="d-flex justify-content-between mb-1 border-bottom pb-1">
                                    <span class="fw-bold" style="font-size: 1.1rem;">{{ $comentario['usuario']['nombre_completo'] }}</span>
                                </div>
                                <p class="mb-0 text-secondary" style="font-size: 1rem;">{{ $comentario['contenido'] }}</p>
                            </div>
                        @empty
                            <p class="text-center text-muted py-2 mb-0" style="font-size: 0.95rem;">No hay comentarios previos.</p>
                        @endforelse
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="{{ $esTecnico ? 'col-lg-3 col-12' : 'col-lg-4 col-12' }}">
        <div class="card border-0 shadow h-100 d-flex flex-column" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-gear-fill me-2"></i>Acciones</h5>
            </div>
            <div class="card-body p-4 text-center d-flex flex-column justify-content-between flex-grow-1">
                <div class="mb-3">
                    <p class="text-muted mb-3 text-uppercase fw-bold ls-1" style="font-size: 1rem;">Estado Actual:</p>
                    <div class="rounded-pill py-4 px-4 d-inline-block fw-bold shadow {{ $esTecnico ? 'bg-success text-white' : 'bg-brown text-white' }}" style="min-width: 150px; font-size: 1.4rem;" data-estado-badge>
                        <i class="bi {{ $esTecnico ? 'bi-check-circle-fill' : 'bi-lock-fill' }} me-2"></i>{{ $ticket['estado']['nombre'] }}
                    </div>
                </div>

                @if($esTecnico)
                <div class="mb-4 p-3 rounded bg-white shadow-sm border-start border-4 border-success">
                    <p class="text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.95rem;">Última actualización:</p>
                    <p class="mb-0 fw-bold text-dark" style="font-size: 1.1rem;" data-ultima-actualizacion>{{ $ticket['updated_at_formatted'] }}</p>
                </div>
                @endif

                <div class="mt-auto">
                    {{-- BOTÓN DINÁMICO SEGÚN ROL --}}
                    @if($esTecnico)
                        <button type="button" class="btn btn-success w-100 py-3 fw-bold shadow" style="font-size: 1rem;" data-bs-toggle="modal" data-bs-target="#modalGestionTicket">
                            <i class="bi bi-check-circle-fill me-2"></i>Actualizar Estado
                        </button>
                    @else
                        <button type="button" class="btn btn-brown w-100 py-3 fw-bold shadow" style="font-size: 1rem;" data-bs-toggle="modal" data-bs-target="#modalGestionTicket">
                            <i class="bi bi-shield-lock-fill me-2"></i>Modificar Estado
                        </button>
                    @endif
                </div>
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
                                <label class="form-label fw-bold text-muted text-uppercase mb-2" style="font-size: 0.95rem;">Usuario operando:</label>
                                <p class="fs-4 mb-0 fw-bold">
                                    <i class="bi bi-person-circle me-2 {{ $esTecnico ? 'text-success' : 'text-brown' }}"></i>
                                    {{ session('usuario_nombre') }}
                                </p>
                                <input type="hidden" name="tecnico_id" value="{{ auth()->id() }}">
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted text-uppercase mb-2" style="font-size: 0.95rem;">Cambiar estado a:</label>
                                <select name="estado_id" class="form-select form-select-lg fw-bold {{ $esTecnico ? 'border-success text-success' : 'border-brown text-brown shadow-sm' }}" style="font-size: 1rem;">
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
                                <small class="text-muted mt-2 d-block" style="font-size: 0.9rem;">
                                    <i class="bi bi-info-circle me-1"></i>
                                    {{ $esTecnico ? 'Opciones limitadas para técnico.' : 'Opciones exclusivas para administrador.' }}
                                </small>
                            </div>
                        </div>

                        <div class="col-md-7 ps-md-4">
                            @if(!$esTecnico)
                            <label class="form-label fw-bold text-muted text-uppercase mb-2" style="font-size: 1rem;">Historial de Comentarios:</label>
                            <div class="bg-light p-3 rounded border mb-3 shadow-inset" style="max-height: 250px; overflow-y: auto;">
                                @forelse($comentarios ?? [] as $comentario)
                                    <div class="mb-2 p-2 bg-white rounded border-start border-4 {{ $esTecnico ? 'border-success' : 'border-brown' }} shadow-sm">
                                        <div class="d-flex justify-content-between mb-1 border-bottom pb-1">
                                            <span class="fw-bold" style="font-size: 1.1rem;">{{ $comentario['usuario']['nombre_completo'] }}</span>
                                            <small class="text-muted" style="font-size: 0.9rem;">{{ \Carbon\Carbon::parse($comentario['created_at'])->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-0 text-secondary" style="font-size: 1rem;">{{ $comentario['contenido'] }}</p>
                                    </div>
                                @empty
                                    <p class="text-center text-muted py-3 mb-0" style="font-size: 0.95rem;">No hay comentarios previos.</p>
                                @endforelse
                            </div>
                            @endif

                            <div class="mb-0">
                                <label class="form-label fw-bold text-muted text-uppercase mb-3" style="font-size: 0.95rem;">Agregar comentario / avance: *</label>
                                <textarea class="form-control form-control-lg {{ $esTecnico ? 'border-success' : 'border-brown' }}" name="contenido" rows="8" placeholder="Indica el motivo del cambio de estado, avances o detalles importantes..." required style="resize: vertical; min-height: 200px; font-size: 0.95rem;"></textarea>
                                <small class="text-muted mt-2 d-block" style="font-size: 0.9rem;">
                                    <i class="bi bi-pencil-square me-1"></i>
                                    Detalla el cambio de estado o cualquier avance en la resolución
                                </small>
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