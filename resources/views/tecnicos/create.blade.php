@extends('layouts.app')

@section('title', 'Registrar Técnico - UPTEX')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary"><i class="bi bi-person-badge me-2"></i>Nuevo Personal Técnico</h2>
        {{-- CAMBIO AQUÍ: 'admin.dashboard' por 'dashboard' --}}
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary fw-bold">Volver</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.tecnicos.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_rol" value="2">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Nombre(s):</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Apellido(s):</label>
                        <input type="text" class="form-control" name="apellido" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold small">Correo Institucional:</label>
                        <input type="email" class="form-control" name="correo" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Contraseña:</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Confirmar Contraseña:</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-5 fw-bold">Registrar Técnico</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection