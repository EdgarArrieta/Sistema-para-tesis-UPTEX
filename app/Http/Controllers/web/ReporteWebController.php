<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Usuario;
use App\Models\Estado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ReporteWebController extends Controller
{
    /**
     * Página principal de reportes (OPTIMIZADO)
     */
    public function index(Request $request)
    {
        try {
            // Obtener filtros de la request
            $area_id = $request->get('area_id', null);
            $estado_id = $request->get('estado_id', null);
            $prioridad_id = $request->get('prioridad_id', null);
            
            // Construir query base (SIN filtro de fecha)
            $ticketsQuery = Ticket::query();
            
            // Aplicar filtros
            if ($area_id) {
                $ticketsQuery->where('area_id', $area_id);
            }
            if ($estado_id) {
                $ticketsQuery->where('estado_id', $estado_id);
            }
            if ($prioridad_id) {
                $ticketsQuery->where('prioridad_id', $prioridad_id);
            }
            
            // Si es una petición AJAX, devolver JSON
            if ($request->ajax()) {
                // Tickets por ÁREA
                $areas_data = (clone $ticketsQuery)
                    ->join('areas', 'tickets.area_id', '=', 'areas.id_area')
                    ->select('areas.nombre', DB::raw('count(*) as total'))
                    ->groupBy('areas.nombre')
                    ->get();
                
                $areas_labels = $areas_data->pluck('nombre')->toArray();
                $areas_data = $areas_data->pluck('total')->toArray();
                
                // Tickets por PRIORIDAD
                $prioridades_data = (clone $ticketsQuery)
                    ->join('prioridades', 'tickets.prioridad_id', '=', 'prioridades.id_prioridad')
                    ->select('prioridades.nombre', DB::raw('count(*) as total'))
                    ->groupBy('prioridades.nombre')
                    ->get();
                
                $prioridades_labels = $prioridades_data->pluck('nombre')->toArray();
                $prioridades_data = $prioridades_data->pluck('total')->toArray();
                
                // Tickets por ESTADO
                $estados_data = (clone $ticketsQuery)
                    ->join('estados', 'tickets.estado_id', '=', 'estados.id_estado')
                    ->select('estados.nombre', DB::raw('count(*) as total'))
                    ->groupBy('estados.nombre')
                    ->get();
                
                $estados_labels = $estados_data->pluck('nombre')->toArray();
                $estados_data = $estados_data->pluck('total')->toArray();
                
                return response()->json([
                    'areas_labels' => $areas_labels,
                    'areas_data' => $areas_data,
                    'prioridades_labels' => $prioridades_labels,
                    'prioridades_data' => $prioridades_data,
                    'estados_labels' => $estados_labels,
                    'estados_data' => $estados_data
                ]);
            }
            
            // Contar tickets
            $total_tickets = $ticketsQuery->count();
            
            // Estadísticas
            $stats = [
                'total_usuarios' => \App\Models\Usuario::where('activo', true)->count(),
                'tecnicos' => \App\Models\Usuario::whereHas('rol', function($query) {
                    $query->where('nombre', 'Técnico');
                })->where('activo', true)->count(),
                'total_tickets' => $total_tickets,
                'tickets_cerrados' => (clone $ticketsQuery)->whereHas('estado', function($q) {
                    $q->where('tipo', 'cerrado');
                })->count(),
                'tickets_abiertos' => (clone $ticketsQuery)->whereHas('estado', function($q) {
                    $q->where('tipo', 'abierto');
                })->count(),
                'tickets_en_proceso' => (clone $ticketsQuery)->whereHas('estado', function($q) {
                    $q->where('tipo', 'en_proceso');
                })->count(),
            ];
            
            // Tickets por ÁREA
            $areas_data = (clone $ticketsQuery)
                ->join('areas', 'tickets.area_id', '=', 'areas.id_area')
                ->select('areas.nombre', DB::raw('count(*) as total'))
                ->groupBy('areas.nombre')
                ->get();
            
            $areas_labels = $areas_data->pluck('nombre')->toArray();
            $areas_data = $areas_data->pluck('total')->toArray();
            
            // Tickets por PRIORIDAD
            $prioridades_data = (clone $ticketsQuery)
                ->join('prioridades', 'tickets.prioridad_id', '=', 'prioridades.id_prioridad')
                ->select('prioridades.nombre', DB::raw('count(*) as total'))
                ->groupBy('prioridades.nombre')
                ->get();
            
            $prioridades_labels = $prioridades_data->pluck('nombre')->toArray();
            $prioridades_data = $prioridades_data->pluck('total')->toArray();
            
            // Tickets por ESTADO
            $estados_data = (clone $ticketsQuery)
                ->join('estados', 'tickets.estado_id', '=', 'estados.id_estado')
                ->select('estados.nombre', DB::raw('count(*) as total'))
                ->groupBy('estados.nombre')
                ->get();
            
            $estados_labels = $estados_data->pluck('nombre')->toArray();
            $estados_data = $estados_data->pluck('total')->toArray();
            
            // Obtener catálogos para los filtros
            $areas = \App\Models\Area::all();
            $estados = \App\Models\Estado::all();
            $prioridades = \App\Models\Prioridad::all();
            
            // Pasar datos a la vista
            return view('reportes.index', compact(
                'stats', 
                'areas_labels', 
                'areas_data', 
                'prioridades_labels', 
                'prioridades_data',
                'estados_labels',
                'estados_data',
                'areas',
                'estados',
                'prioridades',
                'area_id',
                'estado_id',
                'prioridad_id'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Error en reportes: ' . $e->getMessage());
            return view('reportes.index', [
                'stats' => [],
                'areas_labels' => [],
                'areas_data' => [],
                'prioridades_labels' => [],
                'prioridades_data' => [],
                'estados_labels' => [],
                'estados_data' => [],
                'areas' => [],
                'estados' => [],
                'prioridades' => [],
                'area_id' => null,
                'estado_id' => null,
                'prioridad_id' => null
            ]);
        }
    }
    
    /**
     * Reporte por rango de fechas
     */
    public function porFecha(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);
        
        try {
            $tickets = Ticket::with(['usuario', 'area', 'prioridad', 'estado'])
                ->whereBetween('fecha_creacion', [$request->fecha_inicio, $request->fecha_fin])
                ->orderBy('fecha_creacion', 'desc')
                ->get();
            
            $resumen = [
                'total' => $tickets->count(),
                'abiertos' => $tickets->where('estado.tipo', 'abierto')->count(),
                'cerrados' => $tickets->where('estado.tipo', 'cerrado')->count(),
            ];
            
            return view('reportes.por-fecha', compact('tickets', 'resumen', 'request'));
            
        } catch (\Exception $e) {
            \Log::error('Error en reporte por fecha: ' . $e->getMessage());
            return back()->with('error', 'Error al generar el reporte');
        }
    }
    
    /**
     * Rendimiento de técnicos (OPTIMIZADO)
     */
    public function rendimiento()
    {
        try {
            $tecnicos = DB::table('usuarios')
                ->join('roles', 'usuarios.id_rol', '=', 'roles.id_rol')
                ->leftJoin('tickets', 'usuarios.id_usuario', '=', 'tickets.id_tecnico_asignado')
                ->leftJoin('estados', 'tickets.id_estado', '=', 'estados.id_estado')
                ->where('roles.nombre', 'Técnico')
                ->select(
                    'usuarios.id_usuario',
                    'usuarios.nombre',
                    'usuarios.apellido',
                    'usuarios.correo',
                    DB::raw('CONCAT(usuarios.nombre, " ", usuarios.apellido) as tecnico'),
                    DB::raw('COUNT(tickets.id_ticket) as total_asignados'),
                    DB::raw('SUM(CASE WHEN estados.tipo = "cerrado" THEN 1 ELSE 0 END) as cerrados'),
                    DB::raw('SUM(CASE WHEN estados.tipo = "en_proceso" THEN 1 ELSE 0 END) as en_proceso'),
                    DB::raw('SUM(CASE WHEN estados.tipo = "abierto" THEN 1 ELSE 0 END) as abiertos')
                )
                ->groupBy('usuarios.id_usuario', 'usuarios.nombre', 'usuarios.apellido', 'usuarios.correo')
                ->get()
                ->toArray();
            
            return view('reportes.rendimiento', compact('tecnicos'));
            
        } catch (\Exception $e) {
            \Log::error('Error en rendimiento: ' . $e->getMessage());
            return view('reportes.rendimiento', ['tecnicos' => []]);
        }
    }
    
    /**
     * Exportar tickets a CSV (OPTIMIZADO)
     */
    public function exportar()
    {
        try {
            $tickets = DB::table('tickets')
                ->join('usuarios', 'tickets.id_usuario', '=', 'usuarios.id_usuario')
                ->join('areas', 'tickets.id_area', '=', 'areas.id_area')
                ->join('prioridades', 'tickets.id_prioridad', '=', 'prioridades.id_prioridad')
                ->join('estados', 'tickets.id_estado', '=', 'estados.id_estado')
                ->select(
                    'tickets.id_ticket',
                    'tickets.titulo',
                    'tickets.descripcion',
                    DB::raw('CONCAT(usuarios.nombre, " ", usuarios.apellido) as usuario'),
                    'areas.nombre as area',
                    'prioridades.nombre as prioridad',
                    'estados.nombre as estado',
                    'tickets.fecha_creacion',
                    'tickets.fecha_cierre'
                )
                ->orderBy('tickets.fecha_creacion', 'desc')
                ->get();
            
            $filename = 'tickets_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            $callback = function() use ($tickets) {
                $file = fopen('php://output', 'w');
                
                // BOM para UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Encabezados
                fputcsv($file, ['ID', 'Título', 'Descripción', 'Usuario', 'Área', 'Prioridad', 'Estado', 'Fecha Creación', 'Fecha Cierre']);
                
                // Datos
                foreach ($tickets as $ticket) {
                    fputcsv($file, [
                        $ticket->id_ticket,
                        $ticket->titulo,
                        $ticket->descripcion,
                        $ticket->usuario,
                        $ticket->area,
                        $ticket->prioridad,
                        $ticket->estado,
                        $ticket->fecha_creacion,
                        $ticket->fecha_cierre ?? 'N/A'
                    ]);
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            \Log::error('Error al exportar: ' . $e->getMessage());
            return back()->with('error', 'Error al exportar los datos');
        }
    }
}