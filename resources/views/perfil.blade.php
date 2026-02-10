@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-person-circle"></i> Mi Perfil</h2>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="bi bi-person-circle" style="font-size: 5rem; color: var(--primary-color);"></i>
                <h4 class="mt-3">{{ $usuario['nombre'] }} {{ $usuario['apellido'] }}</h4>
                <p class="text-muted">{{ $usuario['correo'] }}</p>
                
                @if($usuario['rol']['nombre'] == 'Administrador')
                    <span class="badge bg-danger fs-6">{{ $usuario['rol']['nombre'] }}</span>
                @elseif($usuario['rol']['nombre'] == 'Técnico')
                    <span class="badge bg-warning fs-6">{{ $usuario['rol']['nombre'] }}</span>
                @else
                    <span class="badge bg-secondary fs-6">{{ $usuario['rol']['nombre'] }}</span>
                @endif
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> Información de Cuenta
            </div>
            <div class="card-body">
                <p><small class="text-muted">Miembro desde:</small><br>
                {{ \Carbon\Carbon::parse($usuario['created_at'])->format('d/m/Y') }}</p>
                
                <p><small class="text-muted">Última actualización:</small><br>
                {{ \Carbon\Carbon::parse($usuario['updated_at'])->diffForHumans() }}</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pencil-square"></i> Editar Información
            </div>
            <div class="card-body">
                <form action="{{ route('perfil.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" 
                                   class="form-control @error('nombre') is-invalid @enderror" 
                                   id="nombre" 
                                   name="nombre" 
                                   value="{{ old('nombre', $usuario['nombre']) }}">
                            @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="apellido" class="form-label">Apellido</label>
                            <input type="text" 
                                   class="form-control @error('apellido') is-invalid @enderror" 
                                   id="apellido" 
                                   name="apellido" 
                                   value="{{ old('apellido', $usuario['apellido']) }}">
                            @error('apellido')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo Electrónico</label>
                        <input type="email" 
                               class="form-control @error('correo') is-invalid @enderror" 
                               id="correo" 
                               name="correo" 
                               value="{{ old('correo', $usuario['correo']) }}">
                        @error('correo')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr>
                    
                    <h6>Cambiar Contraseña</h6>
                    <p class="text-muted small">Deja en blanco si no deseas cambiar tu contraseña</p>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Nueva Contraseña</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password">
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation">
                        </div>
                    </div>
                    
                    <hr>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Guardar Cambios
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection