<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Reporte de tickets por rango de fechas
     */
    public function ticketsPorFecha(Request $request)
    {
        if (!$request->user()->esAdministrador()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para generar reportes',
            ], 403);
        }

        $validated = $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $tickets = Ticket::with(['usuario', 'area', 'prioridad', 'estado', 'tecnicoAsignado'])
            ->whereBetween('fecha_creacion', [$validated['fecha_inicio'], $validated['fecha_fin']])
            ->orderBy('fecha_creacion', 'desc')
            ->get();

        $resumen = [
            'total' => $tickets->count(),
            'abiertos' => $tickets->where('estado.tipo', 'abierto')->count(),
            'cerrados' => $tickets->where('estado.tipo', 'cerrado')->count(),
            'por_prioridad' => $tickets->groupBy('prioridad_id')->map->count(),
            'por_area' => $tickets->groupBy('area_id')->map->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'tickets' => $tickets,
                'resumen' => $resumen,
            ],
        ], 200);
    }

    /**
     * Reporte de rendimiento por técnico
     */
    public function rendimientoTecnicos(Request $request)
    {
        if (!$request->user()->esAdministrador()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para generar reportes',
            ], 403);
        }

        $tecnicos = Usuario::whereHas('rol', function($q) {
            $q->where('nombre', 'Técnico');
        })->with(['ticketsAsignados' => function($q) {
            $q->with(['estado', 'prioridad']);
        }])->get();

        $reporte = $tecnicos->map(function($tecnico) {
            return [
                'tecnico' => $tecnico->nombre_completo,
                'correo' => $tecnico->correo,
                'total_asignados' => $tecnico->ticketsAsignados->count(),
                'cerrados' => $tecnico->ticketsAsignados->where('estado.tipo', 'cerrado')->count(),
                'en_proceso' => $tecnico->ticketsAsignados->where('estado.tipo', 'en_proceso')->count(),
                'abiertos' => $tecnico->ticketsAsignados->where('estado.tipo', 'abierto')->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $reporte,
        ], 200);
    }

    /**
     * Exportar tickets a CSV
     */
    public function exportarCSV(Request $request)
    {
        if (!$request->user()->esAdministrador()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para exportar datos',
            ], 403);
        }

        $tickets = Ticket::with(['usuario', 'area', 'prioridad', 'estado', 'tecnicoAsignado'])
            ->orderBy('fecha_creacion', 'desc')
            ->get();

        $csv = "ID,Título,Usuario,Área,Prioridad,Estado,Técnico,Fecha Creación,Fecha Cierre\n";

        foreach ($tickets as $ticket) {
            $csv .= sprintf(
                "%d,\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",%s,%s\n",
                $ticket->id_ticket,
                str_replace('"', '""', $ticket->titulo),
                $ticket->usuario->nombre_completo,
                $ticket->area->nombre,
                $ticket->prioridad->nombre,
                $ticket->estado->nombre,
                $ticket->tecnicoAsignado ? $ticket->tecnicoAsignado->nombre_completo : 'Sin asignar',
                $ticket->fecha_creacion->format('Y-m-d H:i:s'),
                $ticket->fecha_cierre ? $ticket->fecha_cierre->format('Y-m-d H:i:s') : 'N/A'
            );
        }

        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="tickets_export.csv"');
    }
}