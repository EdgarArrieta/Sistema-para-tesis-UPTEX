<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\ComentarioController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\CatalogoController;
use App\Http\Controllers\Api\ReporteController;

/*
|--------------------------------------------------------------------------
| API Routes - Sistema de Tickets UPTEX
|--------------------------------------------------------------------------
*/

// ========== RUTAS PÚBLICAS ==========
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// ========== RUTAS PROTEGIDAS ==========
Route::middleware('auth:sanctum')->group(function () {
    
    // ========== AUTENTICACIÓN ==========
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });

    // ========== TICKETS ==========
    Route::prefix('tickets')->group(function () {
        Route::get('/', [TicketController::class, 'index']);
        Route::post('/', [TicketController::class, 'store']);
        Route::get('/{id}', [TicketController::class, 'show']);
        
        Route::middleware('role:Administrador,Técnico')->group(function () {
            Route::put('/{id}', [TicketController::class, 'update']);
            Route::put('/{id}/estado', [TicketController::class, 'cambiarEstado']);
            Route::post('/{id}/cerrar', [TicketController::class, 'cerrar']);
        });
        
        Route::middleware('role:Administrador')->group(function () {
            Route::delete('/{id}', [TicketController::class, 'destroy']);
            Route::put('/{id}/asignar', [TicketController::class, 'asignarTecnico']);
        });
        
        Route::get('/{ticketId}/comentarios', [ComentarioController::class, 'index']);
        Route::post('/{ticketId}/comentarios', [ComentarioController::class, 'store']);
    });

    // ========== DASHBOARD ==========
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
        Route::get('/mis-tickets', [DashboardController::class, 'misTickets']);
        Route::get('/asignados', [DashboardController::class, 'ticketsAsignados']);
    });

    // ========== USUARIOS (SOLO ADMIN) ==========
    Route::middleware('role:Administrador')->group(function () {
        Route::prefix('usuarios')->group(function () {
            Route::get('/', [UsuarioController::class, 'index']);
            Route::post('/', [UsuarioController::class, 'store']);
            Route::get('/{id}', [UsuarioController::class, 'show']);
            Route::put('/{id}', [UsuarioController::class, 'update']);
            Route::delete('/{id}', [UsuarioController::class, 'destroy']);
            Route::post('/{id}/toggle-activo', [UsuarioController::class, 'toggleActivo']);
        });
    });

    // ========== CATÁLOGOS ==========
    Route::prefix('catalogos')->group(function () {
        Route::get('/roles', [CatalogoController::class, 'roles']);
        Route::get('/areas', [CatalogoController::class, 'areas']);
        Route::get('/prioridades', [CatalogoController::class, 'prioridades']);
        Route::get('/estados', [CatalogoController::class, 'estados']);
        Route::get('/todos', [CatalogoController::class, 'todos']);
        Route::get('/tecnicos', [UsuarioController::class, 'tecnicos']);
    });

    // ========== REPORTES (SOLO ADMIN) ==========
    Route::middleware('role:Administrador')->group(function () {
        Route::prefix('reportes')->group(function () {
            Route::get('/tickets-fecha', [ReporteController::class, 'ticketsPorFecha']);
            Route::get('/rendimiento-tecnicos', [ReporteController::class, 'rendimientoTecnicos']);
            Route::get('/exportar-csv', [ReporteController::class, 'exportarCSV']);
        });
    });

    // Test
    Route::get('/test-auth', function (Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Autenticación exitosa',
            'user' => $request->user()->nombre_completo,
        ]);
    });
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API Sistema de Tickets UPTEX funcionando',
        'version' => '1.0.0',
        'timestamp' => now()->toDateTimeString(),
    ]);
});