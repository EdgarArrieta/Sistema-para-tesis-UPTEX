<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Estado;
use App\Models\Prioridad;
use App\Models\Area;
use App\Models\Usuario;
use App\Models\Comentario;

class TicketWebController extends Controller
{
    /** 1. ADMINISTRADOR: Mantenido intacto */
    public function index(Request $request)
    {
        try {
            $query = Ticket::with(['usuario', 'area', 'prioridad', 'estado', 'tecnicoAsignado']);
            if (session('usuario_rol') === 'Técnico') {
                $tecnicoId = session('usuario_id');
                $query->where('tecnico_asignado_id', $tecnicoId);
            }
            $ticketsRaw = $query->orderBy('fecha_creacion', 'desc')->paginate(20);
            $tickets = collect();
            foreach ($ticketsRaw->items() as $t) {
                $tickets->push([
                    'id_ticket' => $t->id_ticket,
                    'titulo' => $t->titulo,
                    'descripcion' => $t->descripcion,
                    'fecha_creacion' => $t->fecha_creacion,
                    'usuario' => [ 'nombre_completo' => ($t->usuario->nombre ?? 'N/A') . ' ' . ($t->usuario->apellido ?? '') ],
                    'area' => [ 'nombre' => $t->area->nombre ?? 'N/A' ],
                    'prioridad' => [ 'nombre' => $t->prioridad->nombre ?? 'N/A' ],
                    'estado' => [ 'nombre' => $t->estado->nombre ?? 'N/A', 'tipo' => $t->estado->tipo ?? 'abierto' ],
                ]);
            }
            return view('tickets.index', ['tickets' => $tickets, 'estados' => Estado::all(), 'prioridades' => Prioridad::all(), 'areas' => Area::all(), 'pagination' => $ticketsRaw]);
        } catch (\Exception $e) { return view('tickets.index', ['tickets' => collect([])]); }
    }

    /** 2. DETALLE: Mantenido intacto */
    public function show($id)
    {
        try {
            $t = Ticket::with(['usuario', 'area', 'prioridad', 'estado', 'tecnicoAsignado', 'comentarios.usuario'])->findOrFail($id);
            if (session('usuario_rol') === 'Técnico') {
                $tecnicoId = session('usuario_id');
                if ($t->tecnico_asignado_id != $tecnicoId) {
                    return redirect()->route('tickets.asignados')->with('error', 'No tienes permiso para ver este ticket');
                }
            }
            $ticket = $t->toArray();
            $ticket['usuario']['nombre_completo'] = ($t->usuario->nombre ?? 'N/A') . ' ' . ($t->usuario->apellido ?? '');
            $ticket['area']['nombre'] = $t->area->nombre ?? 'N/A';
            $ticket['prioridad']['nombre'] = $t->prioridad->nombre ?? 'N/A';
            $ticket['estado']['nombre'] = $t->estado->nombre ?? 'N/A';
            $ticket['estado']['tipo'] = $t->estado->tipo ?? 'abierto';
            if (session('usuario_rol') === 'Técnico') {
                $estados = Estado::whereIn('tipo', ['en_proceso', 'pendiente', 'resuelto'])->get();
            } else { $estados = Estado::all(); }
            return view('tickets.show', compact('ticket', 'estados'));
        } catch (\Exception $e) { return redirect()->route('dashboard')->with('error', 'Error al abrir el ticket'); }
    }

    /** 3. EDICIÓN Y ACTUALIZACIÓN: Mantenidos intactos */
    public function edit($id) {
        $ticket = Ticket::findOrFail($id);
        if (session('usuario_rol') === 'Técnico') {
            if ($ticket->tecnico_asignado_id != session('usuario_id')) {
                return redirect()->route('tickets.asignados')->with('error', 'No tienes permiso para editar este ticket');
            }
        }
        return view('tickets.edit', ['ticket' => $ticket, 'estados' => Estado::all(), 'areas' => Area::all(), 'prioridades' => Prioridad::all()]);
    }

    public function update(Request $request, $id) {
        $ticket = Ticket::findOrFail($id);
        if (session('usuario_rol') === 'Técnico') {
            if ($ticket->tecnico_asignado_id != session('usuario_id')) {
                return redirect()->route('tickets.asignados')->with('error', 'No tienes permiso para actualizar este ticket');
            }
        }
        $ticket->update($request->all());
        return redirect()->route('tickets.index')->with('success', 'Ticket actualizado');
    }

    /** 4. PANEL DE TRABAJO (Técnico): ACTUALIZADO CON LAS 8 TARJETAS 
     * Calcula estadísticas exclusivas del técnico asignado.
     */
    public function asignados() { 
        $tecnicoId = session('usuario_id'); 

        // Cálculo de estadísticas para las tarjetas estilo Premium
        $stats = [
            // ESTADOS
            'totales' => Ticket::where('tecnico_asignado_id', $tecnicoId)->count(),
            'en_proceso' => Ticket::where('tecnico_asignado_id', $tecnicoId)
                ->whereHas('estado', function($q){ $q->where('tipo', 'en_proceso'); })->count(),
            'pendientes' => Ticket::where('tecnico_asignado_id', $tecnicoId)
                ->whereHas('estado', function($q){ $q->where('tipo', 'pendiente'); })->count(),
            'resueltos' => Ticket::where('tecnico_asignado_id', $tecnicoId)
                ->whereHas('estado', function($q){ $q->where('tipo', 'resuelto'); })->count(),
            
            // PRIORIDADES
            'baja' => Ticket::where('tecnico_asignado_id', $tecnicoId)
                ->whereHas('prioridad', function($q){ $q->where('nombre', 'Baja'); })->count(),
            'media' => Ticket::where('tecnico_asignado_id', $tecnicoId)
                ->whereHas('prioridad', function($q){ $q->where('nombre', 'Media'); })->count(),
            'alta' => Ticket::where('tecnico_asignado_id', $tecnicoId)
                ->whereHas('prioridad', function($q){ $q->where('nombre', 'Alta'); })->count(),
            'critica' => Ticket::where('tecnico_asignado_id', $tecnicoId)
                ->whereHas('prioridad', function($q){ $q->where('nombre', 'Crítica'); })->count(),
        ];
        
        $tickets_pendientes = Ticket::with(['usuario', 'area', 'prioridad', 'estado'])
            ->where('tecnico_asignado_id', $tecnicoId)
            ->whereHas('estado', function($q){ $q->whereIn('tipo', ['abierto', 'en_proceso', 'pendiente']); })
            ->orderBy('fecha_creacion', 'desc')
            ->get(); 

        foreach ($tickets_pendientes as $ticket) {
            if (!$ticket->usuario) { 
                $ticket->setRelation('usuario', new \App\Models\Usuario(['nombre' => 'N/A', 'apellido' => ''])); 
            }
        }

        return view('tickets.asignados', ['tickets' => $tickets_pendientes, 'stats' => $stats]); 
    }

    /** 5. USUARIO NORMAL: Mantenido intacto */
    public function misTickets() {
        try {
            $usuarioId = session('usuario_id');
            $tickets = Ticket::with(['estado', 'prioridad', 'area'])->where('usuario_id', $usuarioId)->orderBy('fecha_creacion', 'desc')->get();
            return view('tickets.mis-tickets', compact('tickets'));
        } catch (\Exception $e) { return redirect()->route('dashboard'); }
    }

    /** 6. OTROS MÉTODOS: Mantenidos intactos */
    public function create() { return view('tickets.create', ['areas' => Area::all(), 'prioridades' => Prioridad::all()]); }
    public function store(Request $request) {
        Ticket::create([
            'titulo' => $request->titulo, 'descripcion' => $request->descripcion, 'area_id' => $request->area_id,
            'prioridad_id' => $request->prioridad_id, 'usuario_id' => session('usuario_id'), 'estado_id' => 1, 'fecha_creacion' => now()
        ]);
        return redirect()->route('dashboard')->with('success', 'Ticket creado');
    }
}