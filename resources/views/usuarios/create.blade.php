@extends('layouts.app')

@section('title', 'Registrar Usuario - UPTEX')

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4 mt-2 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold"><i class="bi bi-person-plus text-primary me-2"></i> Registrar Nuevo Usuario</h2>
            <p class="text-muted small">Crea un nuevo usuario para el sistema de tickets.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary fw-bold shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver al Panel
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm border-top border-4 border-primary">
                <div class="card-body p-4">
                    <form action="{{ route('admin.usuarios.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Nombre(s):</label>
                                <input type="text" name="nombre" class="form-control bg-light border-0 py-2" required placeholder="Ej. Juan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Apellido(s):</label>
                                <input type="text" name="apellido" class="form-control bg-light border-0 py-2" required placeholder="Ej. Pérez">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-muted text-uppercase">Correo Electrónico:</label>
                                <input type="email" name="correo" class="form-control bg-light border-0 py-2" required placeholder="usuario@correo.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Contraseña:</label>
                                <input type="password" name="password" class="form-control bg-light border-0 py-2" required placeholder="Mínimo 6 caracteres">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted text-uppercase">Confirmar Contraseña:</label>
                                <input type="password" name="password_confirmation" class="form-control bg-light border-0 py-2" required placeholder="Repite la contraseña">
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-top text-end">
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                                <i class="bi bi-save me-1"></i> Guardar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection