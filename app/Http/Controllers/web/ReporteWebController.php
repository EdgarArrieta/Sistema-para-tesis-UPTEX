<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Usuario;
use App\Models\Area;
use App\Models\Prioridad;
use App\Models\Estado;
use Illuminate\Support\Facades\DB;

class ReporteWebController extends Controller
{
    public function index(Request $request) {
        $ticketsQuery = Ticket::query();
        if ($request->ajax()) {
            $areas_data = (clone $ticketsQuery)->join('areas', 'tickets.area_id', '=', 'areas.id_area')->select('areas.nombre', DB::raw('count(*) as total'))->groupBy('areas.nombre')->get();
            return response()->json(['areas_labels' => $areas_data->pluck('nombre'), 'areas_data' => $areas_data->pluck('total')]);
        }
        return view('reportes.index', ['areas' => Area::all(), 'estados' => Estado::all(), 'prioridades' => Prioridad::all()]);
    }

    public function panelAdmin() {
        $ticketsQuery = Ticket::query();
        $stats = [
            'total_usuarios' => Usuario::where('activo', true)->count(),
            'tecnicos' => Usuario::whereHas('rol', function($q){ $q->where('nombre', 'Técnico'); })->count(),
            'usuarios_normales' => Usuario::whereHas('rol', function($q){ $q->where('nombre', 'Usuario Normal')->orWhere('nombre', 'Usuario'); })->count(),
            'total_tickets' => $ticketsQuery->count(),
            'tickets_cerrados' => (clone $ticketsQuery)->whereHas('estado', function($q){ $q->where('tipo', 'cerrado'); })->count(),
            'tickets_abiertos' => (clone $ticketsQuery)->whereHas('estado', function($q){ $q->where('tipo', 'abierto'); })->count(),
            'prioridad_baja' => (clone $ticketsQuery)->whereHas('prioridad', function($q){ $q->where('nombre', 'Baja'); })->count(),
            'prioridad_media' => (clone $ticketsQuery)->whereHas('prioridad', function($q){ $q->where('nombre', 'Media'); })->count(),
            'prioridad_alta' => (clone $ticketsQuery)->whereHas('prioridad', function($q){ $q->where('nombre', 'Alta'); })->count(),
            'prioridad_critica' => (clone $ticketsQuery)->whereHas('prioridad', function($q){ $q->where('nombre', 'Crítica'); })->count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }

    public function exportar() { return response()->json(['ok']); }
    public function rendimiento() { return view('reportes.rendimiento'); }
    public function porFecha() { return view('reportes.por-fecha'); }
}