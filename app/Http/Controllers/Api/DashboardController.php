<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Estado;
use App\Models\Prioridad;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Estadísticas generales del dashboard
     */
    public function index(Request $request)
    {
        $usuario = $request->user();

        // Estadísticas según el rol
        if ($usuario->esUsuarioNormal()) {
            return $this->dashboardUsuario($usuario);
        } elseif ($usuario->esTecnico()) {
            return $this->dashboardTecnico($usuario);
        } else {
            return $this->dashboardAdmin();
        }
    }

    /**
     * Dashboard para Administrador
     */
    private function dashboardAdmin()
    {
        $stats = [
            'total_tickets' => Ticket::count(),
            'tickets_abiertos' => Ticket::whereHas('estado', function($q) {
                $q->where('tipo', Estado::TIPO_ABIERTO);
            })->count(),
            'tickets_en_proceso' => Ticket::whereHas('estado', function($q) {
                $q->where('tipo', Estado::TIPO_EN_PROCESO);
            })->count(),
            'tickets_cerrados' => Ticket::whereHas('estado', function($q) {
                $q->where('tipo', Estado::TIPO_CERRADO);
            })->count(),
            'tickets_por_prioridad' => Ticket::select('prioridad_id', DB::raw('count(*) as total'))
                ->with('prioridad')
                ->groupBy('prioridad_id')
                ->get(),
            'tickets_por_area' => Ticket::select('area_id', DB::raw('count(*) as total'))
                ->with('area')
                ->groupBy('area_id')
                ->get(),
            'tickets_sin_asignar' => Ticket::whereNull('tecnico_asignado_id')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ], 200);
    }

    /**
     * Dashboard para Técnico
     */
    private function dashboardTecnico($usuario)
    {
        $stats = [
            'tickets_asignados' => Ticket::where('tecnico_asignado_id', $usuario->id_usuario)->count(),
            'tickets_abiertos' => Ticket::where('tecnico_asignado_id', $usuario->id_usuario)
                ->whereHas('estado', function($q) {
                    $q->where('tipo', Estado::TIPO_ABIERTO);
                })->count(),
            'tickets_en_proceso' => Ticket::where('tecnico_asignado_id', $usuario->id_usuario)
                ->whereHas('estado', function($q) {
                    $q->where('tipo', Estado::TIPO_EN_PROCESO);
                })->count(),
            'tickets_cerrados' => Ticket::where('tecnico_asignado_id', $usuario->id_usuario)
                ->whereHas('estado', function($q) {
                    $q->where('tipo', Estado::TIPO_CERRADO);
                })->count(),
            'tickets_por_prioridad' => Ticket::where('tecnico_asignado_id', $usuario->id_usuario)
                ->select('prioridad_id', DB::raw('count(*) as total'))
                ->with('prioridad')
                ->groupBy('prioridad_id')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ], 200);
    }

    /**
     * Dashboard para Usuario Normal
     */
    private function dashboardUsuario($usuario)
    {
        $stats = [
            'mis_tickets' => Ticket::where('usuario_id', $usuario->id_usuario)->count(),
            'tickets_abiertos' => Ticket::where('usuario_id', $usuario->id_usuario)
                ->whereHas('estado', function($q) {
                    $q->where('tipo', Estado::TIPO_ABIERTO);
                })->count(),
            'tickets_en_proceso' => Ticket::where('usuario_id', $usuario->id_usuario)
                ->whereHas('estado', function($q) {
                    $q->where('tipo', Estado::TIPO_EN_PROCESO);
                })->count(),
            'tickets_cerrados' => Ticket::where('usuario_id', $usuario->id_usuario)
                ->whereHas('estado', function($q) {
                    $q->where('tipo', Estado::TIPO_CERRADO);
                })->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ], 200);
    }

    /**
     * Mis tickets (para usuarios normales)
     */
    public function misTickets(Request $request)
    {
        $usuario = $request->user();
        
        $tickets = Ticket::where('usuario_id', $usuario->id_usuario)
            ->with(['area', 'prioridad', 'estado', 'tecnicoAsignado'])
            ->orderBy('fecha_creacion', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $tickets,
        ], 200);
    }

    /**
     * Tickets asignados (para técnicos)
     */
    public function ticketsAsignados(Request $request)
    {
        $usuario = $request->user();
        
        $tickets = Ticket::where('tecnico_asignado_id', $usuario->id_usuario)
            ->with(['usuario', 'area', 'prioridad', 'estado'])
            ->orderBy('fecha_creacion', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $tickets,
        ], 200);
    }
}