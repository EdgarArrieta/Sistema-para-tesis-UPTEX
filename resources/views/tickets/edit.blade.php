@extends('layouts.app')

@section('title', 'Editar Ticket')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil-square"></i> Editar Ticket #{{ $ticket['id_ticket'] }}</h2>
    <div>
        <a href="{{ route('tickets.show', $ticket['id_ticket']) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil"></i> Editar Información
            </div>
            <div class="card-body">
                <form action="{{ route('tickets.update', $ticket['id_ticket']) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="titulo" class="form-label fw-semibold">Título *</label>
                        <input type="text" 
                               class="form-control @error('titulo') is-invalid @enderror" 
                               id="titulo" 
                               name="titulo" 
                               value="{{ old('titulo', $ticket['titulo']) }}"
                               required>
                        @error('titulo')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label fw-semibold">Descripción *</label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                  id="descripcion" 
                                  name="descripcion" 
                                  rows="5"
                                  required>{{ old('descripcion', $ticket['descripcion']) }}</textarea>
                        @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="area_id" class="form-label fw-semibold">Área *</label>
                            <select class="form-select @error('area_id') is-invalid @enderror" 
                                    id="area_id" 
                                    name="area_id" 
                                    required>
                                @foreach($areas ?? [] as $area)
                                <option value="{{ $area['id_area'] }}" 
                                        {{ (old('area_id', $ticket['area']['id_area']) == $area['id_area']) ? 'selected' : '' }}>
                                    {{ $area['nombre'] }}
                                </option>
                                @endforeach
                            </select>
                            @error('area_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="prioridad_id" class="form-label fw-semibold">Prioridad *</label>
                            <select class="form-select @error('prioridad_id') is-invalid @enderror" 
                                    id="prioridad_id" 
                                    name="prioridad_id" 
                                    required>
                                @foreach($prioridades ?? [] as $prioridad)
                                <option value="{{ $prioridad['id_prioridad'] }}" 
                                        {{ (old('prioridad_id', $ticket['prioridad']['id_prioridad']) == $prioridad['id_prioridad']) ? 'selected' : '' }}>
                                    {{ $prioridad['nombre'] }}
                                </option>
                                @endforeach
                            </select>
                            @error('prioridad_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    @if(session('usuario_rol') == 'Administrador' || session('usuario_rol') == 'Técnico')
                    <div class="mb-3">
                        <label for="estado_id" class="form-label fw-semibold">Estado</label>
                        <select class="form-select" id="estado_id" name="estado_id">
                            @foreach($estados ?? [] as $estado)
                            <option value="{{ $estado['id_estado'] }}" 
                                    {{ ($ticket['estado']['id_estado'] == $estado['id_estado']) ? 'selected' : '' }}>
                                {{ $estado['nombre'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('tickets.show', $ticket['id_ticket']) }}" class="btn btn-secondary">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Información
            </div>
            <div class="card-body">
                <p class="text-muted mb-1">Fecha de creación:</p>
                <p class="fw-semibold mb-3">{{ \Carbon\Carbon::parse($ticket['fecha_creacion'])->format('d/m/Y H:i') }}</p>
                
                <p class="text-muted mb-1">Estado actual:</p>
                <p>
                    <span class="badge badge-estado-{{ $ticket['estado']['tipo'] }}">
                        {{ $ticket['estado']['nombre'] }}
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection