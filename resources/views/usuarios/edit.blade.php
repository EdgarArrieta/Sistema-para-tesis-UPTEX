@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil-square"></i> Editar Usuario</h2>
    <a href="{{ route('usuarios.show', $usuario['id_usuario']) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-person-badge"></i> Información del Usuario
            </div>
            <div class="card-body">
                <form action="{{ route('usuarios.update', $usuario['id_usuario']) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre *</label>
                            <input type="text" 
                                   class="form-control @error('nombre') is-invalid @enderror" 
                                   id="nombre" 
                                   name="nombre" 
                                   value="{{ old('nombre', $usuario['nombre']) }}"
                                   required>
                            @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="apellido" class="form-label">Apellido *</label>
                            <input type="text" 
                                   class="form-control @error('apellido') is-invalid @enderror" 
                                   id="apellido" 
                                   name="apellido" 
                                   value="{{ old('apellido', $usuario['apellido']) }}"
                                   required>
                            @error('apellido')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo Electrónico *</label>
                        <input type="email" 
                               class="form-control @error('correo') is-invalid @enderror" 
                               id="correo" 
                               name="correo" 
                               value="{{ old('correo', $usuario['correo']) }}"
                               required>
                        @error('correo')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Nueva Contraseña</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password"
                                   placeholder="Dejar en blanco para no cambiar">
                            <small class="text-muted">Solo completa si deseas cambiar la contraseña</small>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="id_rol" class="form-label">Rol *</label>
                            <select class="form-select @error('id_rol') is-invalid @enderror" 
                                    id="id_rol" 
                                    name="id_rol" 
                                    required>
                                @foreach($roles ?? [] as $rol)
                                <option value="{{ $rol['id_rol'] }}" 
                                        {{ (old('id_rol', $usuario['rol']['id_rol']) == $rol['id_rol']) ? 'selected' : '' }}>
                                    {{ $rol['nombre'] }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_rol')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="activo" 
                                   name="activo" 
                                   value="1"
                                   {{ old('activo', $usuario['activo']) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">
                                Usuario activo
                            </label>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('usuarios.show', $usuario['id_usuario']) }}" class="btn btn-secondary">
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
                <p><small class="text-muted">Usuario ID:</small><br>
                <strong>#{{ $usuario['id_usuario'] }}</strong></p>
                
                <p><small class="text-muted">Creado:</small><br>
                {{ \Carbon\Carbon::parse($usuario['created_at'])->format('d/m/Y H:i') }}</p>
                
                <p><small class="text-muted">Última actualización:</small><br>
                {{ \Carbon\Carbon::parse($usuario['updated_at'])->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection