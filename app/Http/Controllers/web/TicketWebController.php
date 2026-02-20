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
    /** 1. LISTADO: Reforzado para técnicos */
    public function index(Request $request)
    {
        try {
            $query = Ticket::with(['usuario', 'area', 'prioridad', 'estado', 'tecnicoAsignado']);
            
            // Si el rol contiene la palabra "Técnico", solo ve sus tickets asignados
            if (str_contains(session('usuario_rol'), 'Técnico')) {
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
            if (str_contains(session('usuario_rol'), 'Técnico')) {
                $tecnicoId = session('usuario_id');
                if ($t->tecnico_asignado_id != $tecnicoId) {
                    return redirect()->route('tickets.index')->with('error', 'No tienes permiso para ver este ticket');
                }
            }
            $ticket = $t->toArray();
            $ticket['usuario']['nombre_completo'] = ($t->usuario->nombre ?? 'N/A') . ' ' . ($t->usuario->apellido ?? '');
            $ticket['area']['nombre'] = $t->area->nombre ?? 'N/A';
            $ticket['prioridad']['nombre'] = $t->prioridad->nombre ?? 'N/A';
            $ticket['estado']['nombre'] = $t->estado->nombre ?? 'N/A';
            $ticket['estado']['tipo'] = $t->estado->tipo ?? 'abierto';
            // Formatear updated_at con la zona horaria correcta
            $ticket['updated_at_formatted'] = $t->updated_at->setTimezone('America/Mexico_City')->format('d/m/Y H:i');
            if (str_contains(session('usuario_rol'), 'Técnico')) {
                $estados = Estado::whereIn('tipo', ['en_proceso', 'pendiente', 'resuelto'])->get();
            } else { $estados = Estado::all(); }
            
            // Obtener comentarios ordenados por fecha descendente
            $comentarios = $t->comentarios()->orderBy('created_at', 'desc')->get();
            
            return view('tickets.show', compact('ticket', 'estados', 'comentarios'));
        } catch (\Exception $e) { return redirect()->route('dashboard')->with('error', 'Error al abrir el ticket'); }
    }

    /** 3. EDICIÓN Y ACTUALIZACIÓN: Mantenidos intactos */
    public function edit($id) {
        $ticket = Ticket::findOrFail($id);
        if (str_contains(session('usuario_rol'), 'Técnico')) {
            if ($ticket->tecnico_asignado_id != session('usuario_id')) {
                return redirect()->route('tickets.index')->with('error', 'No tienes permiso para editar este ticket');
            }
        }
        return view('tickets.edit', ['ticket' => $ticket, 'estados' => Estado::all(), 'areas' => Area::all(), 'prioridades' => Prioridad::all()]);
    }

    public function update(Request $request, $id) {
        $ticket = Ticket::findOrFail($id);
        if (str_contains(session('usuario_rol'), 'Técnico')) {
            if ($ticket->tecnico_asignado_id != session('usuario_id')) {
                return redirect()->route('tickets.index')->with('error', 'No tienes permiso para actualizar este ticket');
            }
        }
        $ticket->update($request->all());
        return redirect()->route('tickets.index')->with('success', 'Ticket actualizado');
    }

    /** 4. PANEL DE TRABAJO TÉCNICO: Mantenido intacto */
    public function asignados() { 
        $tecnicoId = session('usuario_id'); 
        $stats = [
            'totales' => Ticket::where('tecnico_asignado_id', $tecnicoId)->count(),
            'en_proceso' => Ticket::where('tecnico_asignado_id', $tecnicoId)->whereHas('estado', function($q){ $q->where('tipo', 'en_proceso'); })->count(),
            'pendientes' => Ticket::where('tecnico_asignado_id', $tecnicoId)->whereHas('estado', function($q){ $q->where('tipo', 'pendiente'); })->count(),
            'resueltos' => Ticket::where('tecnico_asignado_id', $tecnicoId)->whereHas('estado', function($q){ $q->where('tipo', 'resuelto'); })->count(),
            'baja' => Ticket::where('tecnico_asignado_id', $tecnicoId)->whereHas('prioridad', function($q){ $q->where('nombre', 'Baja'); })->count(),
            'media' => Ticket::where('tecnico_asignado_id', $tecnicoId)->whereHas('prioridad', function($q){ $q->where('nombre', 'Media'); })->count(),
            'alta' => Ticket::where('tecnico_asignado_id', $tecnicoId)->whereHas('prioridad', function($q){ $q->where('nombre', 'Alta'); })->count(),
            'critica' => Ticket::where('tecnico_asignado_id', $tecnicoId)->whereHas('prioridad', function($q){ $q->where('nombre', 'Crítica'); })->count(),
        ];
        $tickets_pendientes = Ticket::with(['usuario', 'area', 'prioridad', 'estado'])->where('tecnico_asignado_id', $tecnicoId)->whereHas('estado', function($q){ $q->whereIn('tipo', ['abierto', 'en_proceso', 'pendiente']); })->orderBy('fecha_creacion', 'desc')->get(); 
        foreach ($tickets_pendientes as $ticket) {
            if (!$ticket->usuario) { $ticket->setRelation('usuario', new \App\Models\Usuario(['nombre' => 'N/A', 'apellido' => ''])); }
        }
        return view('tickets.asignados', ['tickets' => $tickets_pendientes, 'stats' => $stats]); 
    }

    /** 5. USUARIO NORMAL Y TÉCNICO: Ver sus tickets */
    public function misTickets() {
        try {
            $usuarioId = session('usuario_id');
            
            // Si es técnico, ve sus tickets asignados
            if (str_contains(session('usuario_rol'), 'Técnico')) {
                $tickets = Ticket::with(['usuario', 'area', 'prioridad', 'estado', 'tecnicoAsignado'])
                    ->where('tecnico_asignado_id', $usuarioId)
                    ->orderBy('fecha_creacion', 'desc')
                    ->get();
            } else {
                // Si es usuario normal, ve sus propios tickets creados
                $tickets = Ticket::with(['estado', 'prioridad', 'area'])
                    ->where('usuario_id', $usuarioId)
                    ->orderBy('fecha_creacion', 'desc')
                    ->get();
            }
            
            return view('tickets.mis-tickets', compact('tickets'));
        } catch (\Exception $e) { return redirect()->route('dashboard'); }
    }

    /** 6. CREACIÓN: SECCIÓN BLOQUEADA PARA TÉCNICOS */
    public function create() { 
        // BLOQUEO PARA EL ROL TÉCNICO
        if (str_contains(session('usuario_rol'), 'Técnico')) {
            return redirect()->route('tickets.index')->with('error', 'Los técnicos no pueden crear tickets.');
        }
        return view('tickets.create', ['areas' => Area::all(), 'prioridades' => Prioridad::all()]); 
    }

    public function store(Request $request) {
        // BLOQUEO PARA EL ROL TÉCNICO
        if (str_contains(session('usuario_rol'), 'Técnico')) {
            return redirect()->route('tickets.index')->with('error', 'Operación no permitida.');
        }
        Ticket::create([
            'titulo' => $request->titulo, 'descripcion' => $request->descripcion, 'area_id' => $request->area_id,
            'prioridad_id' => $request->prioridad_id, 'usuario_id' => session('usuario_id'), 'estado_id' => 1, 'fecha_creacion' => now()
        ]);
        return redirect()->route('dashboard')->with('success', 'Ticket creado');
    }

    /** 7. CAMBIO DE ESTADO Y COMENTARIOS: Técnico y Admin */
    public function cambiarEstado(Request $request, $id) {
        try {
            $request->validate([
                'estado_id' => 'required|exists:estados,id_estado',
                'contenido' => 'required|string|min:5',
            ]);

            $ticket = Ticket::findOrFail($id);

            // Validar permiso para técnico
            if (str_contains(session('usuario_rol'), 'Técnico')) {
                if ($ticket->tecnico_asignado_id != session('usuario_id')) {
                    return redirect()->route('tickets.show', $id)->with('error', 'No tienes permiso para modificar este ticket');
                }
            }

            // Actualizar estado (Laravel actualiza automáticamente updated_at)
            $ticket->estado_id = $request->estado_id;
            $ticket->save();

            // Si el estado es "Resuelto", registrar fecha de cierre
            $estado = Estado::find($request->estado_id);
            if ($estado && $estado->nombre === 'Resuelto') {
                $ticket->fecha_cierre = now();
                $ticket->save();
            }

            // Crear comentario
            Comentario::create([
                'ticket_id' => $id,
                'usuario_id' => session('usuario_id'),
                'contenido' => $request->contenido,
            ]);

            if (str_contains(session('usuario_rol'), 'Técnico')) {
                return redirect()->route('tickets.show', $id)->with('success', 'Estado actualizado y comentario guardado');
            } else {
                return redirect()->route('tickets.show', $id)->with('success', 'Estado y comentario guardados correctamente');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    // ===== ENDPOINTS API PARA AUTO-REFRESH =====

    /** API: Obtener contadores de tickets */
    public function apiContadores()
    {
        try {
            $tecnicoId = session('usuario_id');
            $estado = Ticket::where('tecnico_asignado_id', $tecnicoId)->with('estado')->get();
            
            $contadores = [
                'total' => $estado->count(),
                'en_proceso' => $estado->filter(fn($t) => $t->estado->nombre === 'En Proceso')->count(),
                'pendiente' => $estado->filter(fn($t) => $t->estado->nombre === 'Pendiente')->count(),
                'resuelto' => $estado->filter(fn($t) => $t->estado->nombre === 'Resuelto')->count(),
            ];
            
            return response()->json($contadores);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /** API: Obtener lista de mis tickets en JSON */
    public function apiMisTickets(Request $request)
    {
        try {
            $tecnicoId = session('usuario_id');
            $query = Ticket::where('tecnico_asignado_id', $tecnicoId)->with(['usuario', 'area', 'prioridad', 'estado']);
            
            // Filtros
            if ($request->estado_id) {
                $query->where('id_estado', $request->estado_id);
            }
            if ($request->prioridad_id) {
                $query->where('id_prioridad', $request->prioridad_id);
            }
            if ($request->search) {
                $search = '%' . $request->search . '%';
                $query->where('titulo', 'like', $search)->orWhere('descripcion', 'like', $search);
            }
            
            $tickets = $query->orderBy('fecha_creacion', 'desc')->get();
            
            $resultado = $tickets->map(function($t) {
                return [
                    'id_ticket' => $t->id_ticket,
                    'titulo' => $t->titulo,
                    'usuario_nombre' => ($t->usuario->nombre ?? 'N/A') . ' ' . ($t->usuario->apellido ?? ''),
                    'prioridad_nombre' => $t->prioridad->nombre ?? 'N/A',
                    'prioridad_nivel' => $t->prioridad->nivel ?? 'media',
                    'estado_nombre' => $t->estado->nombre ?? 'N/A',
                    'estado_tipo' => $t->estado->tipo ?? 'abierto',
                    'fecha_creacion' => \Carbon\Carbon::parse($t->fecha_creacion)->format('d/m/Y H:i'),
                    'fecha_cierre' => $t->fecha_cierre ? \Carbon\Carbon::parse($t->fecha_cierre)->format('d/m/Y H:i') : 'N/A',
                ];
            });
            
            return response()->json($resultado);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /** API: Obtener detalle del ticket en JSON */
    public function apiTicketDetalle($id)
    {
        try {
            $t = Ticket::with(['usuario', 'area', 'prioridad', 'estado', 'tecnicoAsignado'])->findOrFail($id);
            
            return response()->json([
                'id_ticket' => $t->id_ticket,
                'titulo' => $t->titulo,
                'descripcion' => $t->descripcion,
                'usuario_nombre' => ($t->usuario->nombre ?? 'N/A') . ' ' . ($t->usuario->apellido ?? ''),
                'area_nombre' => $t->area->nombre ?? 'N/A',
                'prioridad_nombre' => $t->prioridad->nombre ?? 'N/A',
                'prioridad_nivel' => $t->prioridad->nivel ?? 'media',
                'estado_nombre' => $t->estado->nombre ?? 'N/A',
                'estado_id' => $t->id_estado,
                'fecha_creacion' => \Carbon\Carbon::parse($t->fecha_creacion)->format('d/m/Y H:i'),
                'fecha_cierre' => $t->fecha_cierre ? \Carbon\Carbon::parse($t->fecha_cierre)->format('d/m/Y H:i') : null,
                'updated_at' => \Carbon\Carbon::parse($t->updated_at)->format('d/m/Y H:i'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /** API: Obtener comentarios actualizados de un ticket */
    public function apiComentariosTicket($id)
    {
        try {
            $comentarios = Comentario::where('id_ticket', $id)->with('usuario')->orderBy('created_at', 'asc')->get();
            
            $resultado = $comentarios->map(function($c) {
                return [
                    'id_comentario' => $c->id_comentario,
                    'usuario_nombre' => ($c->usuario->nombre ?? 'N/A') . ' ' . ($c->usuario->apellido ?? ''),
                    'contenido' => $c->contenido,
                    'created_at' => \Carbon\Carbon::parse($c->created_at)->format('d/m/Y H:i'),
                ];
            });
            
            return response()->json($resultado);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}