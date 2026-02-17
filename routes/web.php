<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\WebController;
use App\Http\Controllers\Web\TicketWebController;
use App\Http\Controllers\Web\UsuarioWebController;
use App\Http\Controllers\Web\ReporteWebController;

// Ruta raíz redirige a login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas públicas de autenticación
Route::get('/login', [WebController::class, 'showLogin'])->name('login');
Route::post('/login', [WebController::class, 'login'])->name('login.post');

// Rutas de registro (solo para usuarios normales)
Route::get('/register', [WebController::class, 'showRegister'])->name('register');
Route::post('/register', [WebController::class, 'register'])->name('register.post');

// Rutas protegidas (requieren autenticación)
Route::middleware('web.auth')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [WebController::class, 'dashboard'])->name('dashboard');
    
    // Logout
    Route::post('/logout', [WebController::class, 'logout'])->name('logout');
    
    // Perfil
    Route::get('/perfil', [WebController::class, 'perfil'])->name('perfil');
    Route::put('/perfil', [WebController::class, 'updatePerfil'])->name('perfil.update');
    
    // Tickets
    Route::resource('tickets', TicketWebController::class, ['only' => ['show', 'edit', 'update', 'store', 'destroy', 'create']]);
    Route::post('/tickets/{id}/asignar', [TicketWebController::class, 'asignar'])->name('tickets.asignar');
    Route::post('/tickets/{id}/cambiar-estado', [TicketWebController::class, 'cambiarEstado'])->name('tickets.cambiar-estado');
    Route::post('/tickets/{id}/cerrar', [TicketWebController::class, 'cerrar'])->name('tickets.cerrar');
    Route::post('/tickets/{id}/comentarios', [TicketWebController::class, 'storeComentario'])->name('tickets.comentarios.store');
    Route::get('/mis-tickets', [TicketWebController::class, 'misTickets'])->name('tickets.mis-tickets');
    
    // Tickets - Solo Técnicos
    Route::middleware('web.tecnico')->group(function () {
        Route::get('/tickets-asignados', [TicketWebController::class, 'asignados'])->name('tickets.asignados');
        Route::get('/historial-tickets', [TicketWebController::class, 'misTicketsHistorial'])->name('tickets.historial');
    });
    
    // Tickets - Solo Admin (index)
    Route::middleware('web.admin')->group(function () {
        Route::get('/tickets', [TicketWebController::class, 'index'])->name('tickets.index');
    });
    
    // Usuarios (solo admin)
    Route::middleware('web.admin')->group(function () {
        Route::resource('usuarios', UsuarioWebController::class);
        Route::post('/usuarios/{id}/toggle-activo', [UsuarioWebController::class, 'toggleActivo'])->name('usuarios.toggle-activo');
    });
    
    // Reportes (solo admin)
    Route::middleware('web.admin')->prefix('reportes')->group(function () {
        Route::get('/', [ReporteWebController::class, 'index'])->name('reportes.index');
        Route::get('/por-fecha', [ReporteWebController::class, 'porFecha'])->name('reportes.por-fecha');
        Route::get('/rendimiento', [ReporteWebController::class, 'rendimiento'])->name('reportes.rendimiento');
        Route::get('/exportar', [ReporteWebController::class, 'exportar'])->name('reportes.exportar');
    });

    Route::get('/tecnico/boleta-ticket/{id}', [App\Http\Controllers\Web\TicketWebController::class, 'verFichaTecnica'])->name('tecnicos.ver-ticket');

    // Ruta para cambiar el estado (la que usa el botón verde)
Route::post('/tickets/{id}/cambiar-estado', [App\Http\Controllers\Web\TicketWebController::class, 'cambiarEstado'])->name('tickets.cambiar-estado');

// Ruta para los comentarios (la que causó el error rojo)
Route::post('/tickets/{id}/comentario', [App\Http\Controllers\Web\TicketWebController::class, 'storeComentario'])->name('tickets.comentario.store');
});