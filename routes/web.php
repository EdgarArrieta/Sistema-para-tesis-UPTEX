<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\WebController;
use App\Http\Controllers\Web\TicketWebController;
use App\Http\Controllers\Web\UsuarioWebController;
use App\Http\Controllers\Web\ReporteWebController;

// --- INICIO ---
Route::get('/', function () { return redirect()->route('login'); });

// --- AUTENTICACIÓN PÚBLICA ---
Route::get('/login', [WebController::class, 'showLogin'])->name('login');
Route::post('/login', [WebController::class, 'login'])->name('login.post');
Route::get('/register', [WebController::class, 'showRegister'])->name('register');
Route::post('/register', [WebController::class, 'register'])->name('register.post');

// --- RUTAS PROTEGIDAS ---
Route::middleware('web.auth')->group(function () {
    
    Route::get('/dashboard', [WebController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [WebController::class, 'logout'])->name('logout');
    Route::get('/perfil', [WebController::class, 'perfil'])->name('perfil');
    Route::put('/perfil', [WebController::class, 'updatePerfil'])->name('perfil.update');
    
    // GESTIÓN DE TICKETS (Se añaden las rutas que faltaban para el botón azul)
    Route::resource('tickets', TicketWebController::class);
    Route::post('/tickets/{id}/asignar', [TicketWebController::class, 'asignar'])->name('tickets.asignar');
    Route::post('/tickets/{id}/cambiar-estado', [TicketWebController::class, 'cambiarEstado'])->name('tickets.cambiar-estado');
    Route::post('/tickets/{id}/cerrar', [TicketWebController::class, 'cerrar'])->name('tickets.cerrar');
    Route::post('/tickets/{id}/comentarios', [TicketWebController::class, 'storeComentario'])->name('tickets.comentarios.store');
    Route::get('/mis-tickets', [TicketWebController::class, 'misTickets'])->name('tickets.mis-tickets');

    // --- SOLO ADMINISTRADORES (UPTEX) ---
    Route::middleware('web.admin')->group(function () {
        Route::get('/admin/ver-tickets', [TicketWebController::class, 'index'])->name('tickets.index');
        Route::resource('usuarios', UsuarioWebController::class);
        Route::post('/usuarios/{id}/toggle-activo', [UsuarioWebController::class, 'toggleActivo'])->name('usuarios.toggle-activo');

        Route::get('/reportes', [ReporteWebController::class, 'index'])->name('reportes.index');
        Route::prefix('reportes')->name('reportes.')->group(function () {
            Route::get('/por-fecha', [ReporteWebController::class, 'porFecha'])->name('por-fecha');
            Route::get('/rendimiento', [ReporteWebController::class, 'rendimiento'])->name('rendimiento');
            Route::get('/exportar', [ReporteWebController::class, 'exportar'])->name('exportar');
        });

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/tecnicos/crear', [UsuarioWebController::class, 'createTecnico'])->name('tecnicos.create');
            Route::post('/tecnicos/guardar', [UsuarioWebController::class, 'storeTecnico'])->name('tecnicos.store');
            Route::get('/usuarios/crear', [UsuarioWebController::class, 'createUsuario'])->name('usuarios.create');
            Route::post('/usuarios/guardar', [UsuarioWebController::class, 'storeUsuario'])->name('usuarios.store');
        });
    });

    // --- SOLO TÉCNICOS ---
    Route::middleware('web.tecnico')->group(function () {
        Route::get('/tickets-asignados', [TicketWebController::class, 'asignados'])->name('tickets.asignados');
        Route::get('/historial-tickets', [TicketWebController::class, 'misTicketsHistorial'])->name('tickets.historial');
        Route::get('/tecnico/boleta-ticket/{id}', [TicketWebController::class, 'verFichaTecnica'])->name('tecnicos.ver-ticket');
    });
});