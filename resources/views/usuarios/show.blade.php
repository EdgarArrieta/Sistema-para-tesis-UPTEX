@extends('layouts.app')

@section('title', 'Detalle del Usuario')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-person-badge"></i> Detalle del Usuario</h2>
    <div>
        <a href="{{ route('usuarios.edit', $usuario['id_usuario']) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-person-circle" style="font-size: 5rem; color: var(--primary-color);"></i>
                </div>
                <h4>{{ $usuario['nombre'] }} {{ $usuario['apellido'] }}</h4>
                <p class="text-muted">{{ $usuario['correo'] }}</p>
                
                @if($usuario['rol']['nombre'] == 'Administrador')
                    <span class="badge bg-danger fs-6">{{ $usuario['rol']['nombre'] }}</span>
                @elseif($usuario['rol']['nombre'] == 'Técnico')
                    <span class="badge bg-warning fs-6">{{ $usuario['rol']['nombre'] }}</span>
                @else
                    <span class="badge bg-secondary fs-6">{{ $usuario['rol']['nombre'] }}</span>
                @endif
                
                <hr>
                
                <div class="d-grid gap-2">
                    @if($usuario['activo'])
                        <span class="badge bg-success fs-6 py-2">
                            <i class="bi bi-check-circle"></i> Usuario Activo
                        </span>
                    @else
                        <span class="badge bg-danger fs-6 py-2">
                            <i class="bi bi-x-circle"></i> Usuario Inactivo
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <i class="bi bi-clock-history"></i> Información de Cuenta
            </div>
            <div class="card-body">
                <p><small class="text-muted">Creado:</small><br>
                {{ \Carbon\Carbon::parse($usuario['created_at'])->format('d/m/Y H:i') }}</p>
                
                <p><small class="text-muted">Última actualización:</small><br>
                {{ \Carbon\Carbon::parse($usuario['updated_at'])->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <!-- Estadísticas de Tickets -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-bar-chart"></i> Estadísticas de Tickets
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="stat-card primary">
                            <div class="stat-number">{{ count($usuario['tickets'] ?? []) }}</div>
                            <div class="stat-label">
                                <i class="bi bi-ticket"></i> Tickets Creados
                            </div>
                        </div>
                    </div>
                    
                    @if($usuario['rol']['nombre'] == 'Técnico')
                    <div class="col-md-6 mb-3">
                        <div class="stat-card warning">
                            <div class="stat-number">{{ count($usuario['tickets_asignados'] ?? []) }}</div>
                            <div class="stat-label">
                                <i class="bi bi-clipboard-check"></i> Tickets Asignados
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Tickets Recientes -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-ticket"></i> Tickets Recientes
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(collect($usuario->tickets ?? [])->take(5) as $ticket)
                            <tr>
                                <td>#{{ $ticket['id_ticket'] }}</td>
                                <td>{{ Str::limit($ticket['titulo'], 40) }}</td>
                                <td>
                                    <span class="badge badge-estado-{{ $ticket['estado']['tipo'] ?? 'abierto' }}">
                                        {{ $ticket['estado']['nombre'] ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($ticket['fecha_creacion'])->format('d/m/Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    No hay tickets recientes
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection