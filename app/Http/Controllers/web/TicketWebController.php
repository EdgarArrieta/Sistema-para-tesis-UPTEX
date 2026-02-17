<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Ticket;

class TicketWebController extends Controller
{
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('app.url') . '/api';
    }

    /**
     * Listar tickets (OPTIMIZADO con paginación)
     */
    public function index(Request $request)
    {
        try {
            // Query base con selects específicos
            $query = Ticket::select('tickets.*')
                ->with(['usuario:id_usuario,nombre,apellido', 
                        'area:id_area,nombre', 
                        'prioridad:id_prioridad,nombre,nivel', 
                        'estado:id_estado,nombre,tipo',
                        'tecnicoAsignado:id_usuario,nombre,apellido']);
            
            // Filtros
            if ($request->filled('estado_id')) {
                $query->where('estado_id', $request->estado_id);
            }
            
            if ($request->filled('prioridad_id')) {
                $query->where('prioridad_id', $request->prioridad_id);
            }
            
            if ($request->filled('area_id')) {
                $query->where('area_id', $request->area_id);
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('titulo', 'like', "%{$search}%")
                      ->orWhere('id_ticket', 'like', "%{$search}%");
                });
            }
            
            // Obtener tickets con paginación (20 por página)
            $ticketsPaginated = $query->orderBy('fecha_creacion', 'desc')
                ->paginate(20);
            
            // Convertir a arrays para la vista
            $tickets = collect();
            foreach ($ticketsPaginated->items() as $ticket) {
                $tickets->push([
                    'id_ticket' => $ticket->id_ticket,
                    'titulo' => $ticket->titulo,
                    'descripcion' => $ticket->descripcion,
                    'fecha_creacion' => $ticket->fecha_creacion,
                    'usuario' => [
                        'id_usuario' => $ticket->usuario->id_usuario,
                        'nombre_completo' => $ticket->usuario->nombre . ' ' . $ticket->usuario->apellido,
                    ],
                    'area' => [
                        'id_area' => $ticket->area->id_area,
                        'nombre' => $ticket->area->nombre,
                    ],
                    'prioridad' => [
                        'id_prioridad' => $ticket->prioridad->id_prioridad,
                        'nombre' => $ticket->prioridad->nombre,
                        'nivel' => $ticket->prioridad->nivel,
                    ],
                    'estado' => [
                        'id_estado' => $ticket->estado->id_estado,
                        'nombre' => $ticket->estado->nombre,
                        'tipo' => $ticket->estado->tipo,
                    ],
                    'tecnico_asignado' => $ticket->tecnicoAsignado ? [
                        'id_usuario' => $ticket->tecnicoAsignado->id_usuario,
                        'nombre_completo' => $ticket->tecnicoAsignado->nombre . ' ' . $ticket->tecnicoAsignado->apellido,
                    ] : null,
                ]);
            }
            
            // Si es solicitud AJAX, retornar JSON
            if ($request->ajax()) {
                return response()->json([
                    'tickets' => $tickets->toArray(),
                    'total' => $ticketsPaginated->total(),
                ]);
            }
            
            // Catálogos sin caché para evitar problemas
            $estados = \App\Models\Estado::select('id_estado', 'nombre', 'tipo')->get();
            $prioridades = \App\Models\Prioridad::select('id_prioridad', 'nombre', 'nivel')->get();
            $areas = \App\Models\Area::select('id_area', 'nombre')->get();
            
            return view('tickets.index', [
                'tickets' => $tickets,
                'pagination' => [
                    'total' => $ticketsPaginated->total(),
                    'per_page' => $ticketsPaginated->perPage(),
                    'current_page' => $ticketsPaginated->currentPage(),
                    'last_page' => $ticketsPaginated->lastPage(),
                    'links' => $ticketsPaginated->links(),
                ],
                'estados' => $estados,
                'prioridades' => $prioridades,
                'areas' => $areas,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al cargar tickets: ' . $e->getMessage());
            
            // Si es solicitud AJAX, retornar JSON
            if ($request->ajax()) {
                return response()->json([
                    'tickets' => [],
                    'total' => 0,
                    'error' => 'Error al cargar tickets'
                ], 500);
            }
            
            return view('tickets.index', [
                'tickets' => collect([]),
                'pagination' => null,
                'estados' => collect([]),
                'prioridades' => collect([]),
                'areas' => collect([]),
            ])->with('error', 'Error al cargar tickets');
        }
    }
 /**
     * Mostrar formulario de crear ticket (OPTIMIZADO)
     */
    public function create()
    {
        // Obtener catálogos sin caché para datos frescos
        $areas = \App\Models\Area::select('id_area', 'nombre')->get()->toArray();
        $prioridades = \App\Models\Prioridad::select('id_prioridad', 'nombre', 'nivel')->get()->toArray();
        
        return view('tickets.create', compact('areas', 'prioridades'));
    }

    /**
     * Guardar nuevo ticket
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'required|string',
            'area_id' => 'required|exists:areas,id_area',
            'prioridad_id' => 'required|exists:prioridades,id_prioridad',
        ], [
            'titulo.required' => 'El título es obligatorio',
            'descripcion.required' => 'La descripción es obligatoria',
            'area_id.required' => 'Debe seleccionar un área',
            'prioridad_id.required' => 'Debe seleccionar una prioridad',
        ]);
        
        try {
            // Obtener estado "Abierto"
            $estadoAbierto = \App\Models\Estado::where('tipo', 'abierto')->first();
            
            if (!$estadoAbierto) {
                return back()->withInput()->with('error', 'Error en la configuración del sistema');
            }
            
            // Crear ticket
            $ticket = new \App\Models\Ticket();
            $ticket->titulo = $request->titulo;
            $ticket->descripcion = $request->descripcion;
            $ticket->area_id = $request->area_id;
            $ticket->prioridad_id = $request->prioridad_id;
            $ticket->usuario_id = session('usuario_id');
            $ticket->estado_id = $estadoAbierto->id_estado;
            $ticket->fecha_creacion = now();
            $ticket->save();
            
            return redirect()->route('tickets.show', $ticket->id_ticket)
                ->with('success', 'Ticket creado exitosamente');
                
        } catch (\Exception $e) {
            \Log::error('Error al crear ticket: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withInput()->with('error', 'Error al crear el ticket: ' . $e->getMessage());
        }
    }
    /**
     * Mostrar detalle del ticket
     */
    public function show($id)
    {
        try {
            // Obtener ticket con TODAS las relaciones en una sola query (Eager Loading)
            $ticket = \App\Models\Ticket::select('tickets.*')
                ->with([
                    'usuario:id_usuario,nombre,apellido',
                    'area:id_area,nombre',
                    'prioridad:id_prioridad,nombre,nivel',
                    'estado:id_estado,nombre,tipo',
                    'tecnicoAsignado:id_usuario,nombre,apellido',
                    'comentarios:id_comentario,ticket_id,usuario_id,contenido,created_at'
                ])
                ->findOrFail($id);
            
            // Validar permiso: técnico solo puede ver sus propios tickets asignados
            $usuario = auth()->user();
            if ($usuario && $usuario->rol && $usuario->rol->nombre === 'Técnico') {
                if ($ticket->tecnico_asignado_id !== $usuario->id_usuario) {
                    return redirect()->route('tickets.asignados')
                        ->with('error', 'No tienes permiso para ver este ticket');
                }
            }
            
            // Obtener comentarios y cargar relación con usuario
            $comentarios = $ticket->comentarios;
            
            // Cargar usuario para cada comentario si no está cargado
            if ($comentarios && $comentarios->count() > 0) {
                $comentarios->load('usuario');
            }
            
            // Obtener catálogos con select específicos para optimizar
            $estados = \App\Models\Estado::select('id_estado', 'nombre', 'tipo')->get();
            $tecnicos = \App\Models\Usuario::select('id_usuario', 'nombre', 'apellido')
                ->whereHas('rol', function($q) {
                    $q->where('nombre', 'Técnico');
                })->get();
            
            // Convertir a formato array para la vista
            $ticketData = [
                'id_ticket' => $ticket->id_ticket,
                'titulo' => $ticket->titulo,
                'descripcion' => $ticket->descripcion,
                'fecha_creacion' => $ticket->fecha_creacion,
                'fecha_cierre' => $ticket->fecha_cierre,
                'solucion' => $ticket->solucion,
                'updated_at' => $ticket->updated_at,
                'usuario' => [
                    'id_usuario' => $ticket->usuario->id_usuario,
                    'nombre_completo' => $ticket->usuario->nombre_completo,
                ],
                'area' => [
                    'id_area' => $ticket->area->id_area,
                    'nombre' => $ticket->area->nombre,
                ],
                'prioridad' => [
                    'id_prioridad' => $ticket->prioridad->id_prioridad,
                    'nombre' => $ticket->prioridad->nombre,
                    'nivel' => $ticket->prioridad->nivel,
                ],
                'estado' => [
                    'id_estado' => $ticket->estado->id_estado,
                    'nombre' => $ticket->estado->nombre,
                    'tipo' => $ticket->estado->tipo,
                ],
                'tecnico_asignado' => $ticket->tecnicoAsignado ? [
                    'id' => $ticket->tecnicoAsignado->id_usuario,
                    'nombre_completo' => $ticket->tecnicoAsignado->nombre_completo,
                ] : null,
            ];
            
            // Convertir comentarios a array
            $comentariosData = $comentarios->map(function($com) {
                return [
                    'id_comentario' => $com->id_comentario,
                    'contenido' => $com->contenido,
                    'created_at' => $com->created_at,
                    'usuario' => [
                        'id_usuario' => $com->usuario->id_usuario,
                        'nombre_completo' => $com->usuario->nombre_completo,
                    ],
                ];
            })->toArray();
            
            // Convertir estados
            $estadosData = $estados->map(function($est) {
                return [
                    'id_estado' => $est->id_estado,
                    'nombre' => $est->nombre,
                    'tipo' => $est->tipo,
                ];
            })->toArray();
            
            // Convertir técnicos
            $tecnicosData = $tecnicos->map(function($tec) {
                return [
                    'id_usuario' => $tec->id_usuario,
                    'nombre_completo' => $tec->nombre_completo,
                ];
            })->toArray();
            
            return view('tickets.show', [
                'ticket' => $ticketData,
                'comentarios' => $comentariosData,
                'estados' => $estadosData,
                'tecnicos' => $tecnicosData,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al cargar ticket: ' . $e->getMessage());
            return redirect()->route('tickets.asignados')
                ->with('error', 'Error al cargar el ticket');
        }
    }
/**
     * Mostrar formulario de editar
     */
    public function edit($id)
    {
        try {
            $ticket = Ticket::with(['area', 'prioridad', 'estado'])
                ->findOrFail($id);
            
            // Validar permiso: técnico solo puede editar sus propios tickets asignados
            $usuario = auth()->user();
            if ($usuario && $usuario->rol && $usuario->rol->nombre === 'Técnico') {
                if ($ticket->tecnico_asignado_id !== $usuario->id_usuario) {
                    return redirect()->route('tickets.asignados')
                        ->with('error', 'No tienes permiso para editar este ticket');
                }
            }
            
            // Convertir a array para compatibilidad
            $ticket = $ticket->toArray();
            
            // Obtener catálogos
            $estados = \App\Models\Estado::all()->toArray();
            $prioridades = \App\Models\Prioridad::all()->toArray();
            $areas = \App\Models\Area::all()->toArray();
            
            return view('tickets.edit', compact('ticket', 'estados', 'prioridades', 'areas'));
            
        } catch (\Exception $e) {
            \Log::error('Error al cargar ticket para editar: ' . $e->getMessage());
            return redirect()->route('tickets.asignados')
                ->with('error', 'Error al cargar el ticket');
        }
    }

   /**
     * Actualizar ticket
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'required|string',
            'area_id' => 'required|exists:areas,id_area',
            'prioridad_id' => 'required|exists:prioridades,id_prioridad',
        ]);
        
        try {
            $ticket = Ticket::findOrFail($id);
            
            // Validar permiso
            $usuario = auth()->user();
            if ($usuario && $usuario->rol && $usuario->rol->nombre === 'Técnico') {
                if ($ticket->tecnico_asignado_id !== $usuario->id_usuario) {
                    return redirect()->route('tickets.asignados')
                        ->with('error', 'No tienes permiso para editar este ticket');
                }
            }
            
            $ticket->titulo = $request->titulo;
            $ticket->descripcion = $request->descripcion;
            $ticket->area_id = $request->area_id;
            $ticket->prioridad_id = $request->prioridad_id;
            
            if ($request->filled('estado_id')) {
                $ticket->estado_id = $request->estado_id;
            }
            
            $ticket->save();
            
            return redirect()->route('tickets.show', $id)
                ->with('success', 'Ticket actualizado exitosamente');
                
        } catch (\Exception $e) {
            \Log::error('Error al actualizar ticket: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error al actualizar el ticket');
        }
    }
    /**
     * Eliminar ticket
     */
    public function destroy($id)
    {
        try {
            // Verificar que el ticket existe
            $ticket = \App\Models\Ticket::find($id);
            
            if (!$ticket) {
                return redirect()->route('tickets.index')
                    ->with('error', 'Ticket no encontrado');
            }
            
            // Eliminar comentarios relacionados primero (si existen)
            try {
                \App\Models\Comentario::where('id_ticket', $id)->delete();
            } catch (\Exception $e) {
                \Log::warning('Advertencia al eliminar comentarios: ' . $e->getMessage());
            }
            
            // Eliminar ticket
            if ($ticket->delete()) {
                return redirect()->route('tickets.index')
                    ->with('success', 'Ticket eliminado exitosamente');
            } else {
                return redirect()->route('tickets.index')
                    ->with('error', 'No se pudo eliminar el ticket');
            }
                
        } catch (\Exception $e) {
            \Log::error('Error al eliminar ticket: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->route('tickets.index')
                ->with('error', 'Error al eliminar el ticket');
        }
    }    /**
     * Asignar técnico
     */
    public function asignar(Request $request, $id)
    {
        try {
            $ticket = Ticket::find($id);
            
            if (!$ticket) {
                return back()->with('error', 'Ticket no encontrado');
            }
            
            $ticket->tecnico_asignado_id = $request->tecnico_id;
            $ticket->save();
            
            return back()->with('success', 'Técnico asignado exitosamente');
        } catch (\Exception $e) {
            \Log::error('Error al asignar técnico: ' . $e->getMessage());
            return back()->with('error', 'Error al asignar técnico');
        }
    }

    /**
     * Cambiar estado
     */
    public function cambiarEstado(Request $request, $id)
    {
        try {
            $ticket = Ticket::find($id);
            
            if (!$ticket) {
                return back()->with('error', 'Ticket no encontrado');
            }
            
            // Validar permiso
            $usuario = auth()->user();
            if ($usuario && $usuario->rol && $usuario->rol->nombre === 'Técnico') {
                if ($ticket->tecnico_asignado_id !== $usuario->id_usuario) {
                    return back()->with('error', 'No tienes permiso para cambiar el estado de este ticket');
                }
            }
            
            $ticket->estado_id = $request->estado_id;
            $ticket->save();
            
            return back()->with('success', 'Estado actualizado exitosamente');
        } catch (\Exception $e) {
            \Log::error('Error al cambiar estado: ' . $e->getMessage());
            return back()->with('error', 'Error al cambiar estado');
        }
    }

    /**
     * Cerrar ticket
     */
    public function cerrar(Request $request, $id)
    {
        try {
            $ticket = Ticket::find($id);
            
            if (!$ticket) {
                return back()->with('error', 'Ticket no encontrado');
            }
            
            // Validar permiso
            $usuario = auth()->user();
            if ($usuario && $usuario->rol && $usuario->rol->nombre === 'Técnico') {
                if ($ticket->tecnico_asignado_id !== $usuario->id_usuario) {
                    return back()->with('error', 'No tienes permiso para cerrar este ticket');
                }
            }
            
            $ticket->estado_id = $request->estado_id;
            $ticket->tecnico_asignado_id = $request->tecnico_asignado_id;
            $ticket->solucion = $request->solucion;
            $ticket->fecha_cierre = now();
            $ticket->save();
            
            return back()->with('success', 'Ticket cerrado exitosamente');
        } catch (\Exception $e) {
            \Log::error('Error al cerrar ticket: ' . $e->getMessage());
            return back()->with('error', 'Error al cerrar ticket');
        }
    }

    /**
     * Agregar comentario
     */
    public function storeComentario(Request $request, $id)
    {
        $request->validate([
            'contenido' => 'required|string'
        ]);

        try {
            // Verificar que el ticket existe
            $ticket = \App\Models\Ticket::findOrFail($id);
            
            // Verificar permiso: usuario solo puede comentar sus propios tickets, técnico solo los asignados
            $usuario = auth()->user();
            $esTecnico = $usuario && $usuario->rol && $usuario->rol->nombre === 'Técnico';
            
            if ($esTecnico && $ticket->tecnico_asignado_id !== $usuario->id_usuario) {
                return back()->with('error', 'No tienes permiso para comentar este ticket');
            }
            
            if (!$esTecnico && $ticket->usuario_id !== $usuario->id_usuario) {
                return back()->with('error', 'No tienes permiso para comentar este ticket');
            }
            
            // Crear comentario
            $comentario = new \App\Models\Comentario();
            $comentario->ticket_id = $id;
            $comentario->usuario_id = $usuario->id_usuario;
            $comentario->contenido = $request->contenido;
            $comentario->save();
            
            return back()->with('success', 'Comentario agregado exitosamente');
            
        } catch (\Exception $e) {
            \Log::error('Error al agregar comentario: ' . $e->getMessage());
            return back()->with('error', 'Error al agregar comentario');
        }
    }

    /**
     * Mis tickets (Usuario normal)
     */
    public function misTickets()
    {
        try {
            $usuarioId = session('usuario_id');
            
            // Obtener tickets del usuario con relaciones y paginación
            $ticketsPaginated = \App\Models\Ticket::where('usuario_id', $usuarioId)
                ->with([
                    'usuario:id_usuario,nombre,apellido',
                    'area:id_area,nombre',
                    'prioridad:id_prioridad,nombre,nivel',
                    'estado:id_estado,nombre,tipo',
                    'tecnicoAsignado:id_usuario,nombre,apellido'
                ])
                ->orderBy('fecha_creacion', 'desc')
                ->paginate(20);
            
            $catalogos = $this->getCatalogos();
            
            return view('tickets.mis-tickets', array_merge([
                'tickets'    => $ticketsPaginated->items(),
                'pagination' => $ticketsPaginated,
            ], $catalogos));

        } catch (\Exception $e) {
            \Log::error('Error al cargar mis tickets: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Error al cargar tickets');
        }
    }

    /**
     * Tickets asignados al técnico (mejorado)
     */
    public function asignados()
    {
        try {
            $tecnicoId = session('usuario_id');

            // Tickets pendientes (abiertos) - OPTIMIZADO
            $tickets_pendientes = \App\Models\Ticket::select('tickets.*')
                ->with(['usuario:id_usuario,nombre,apellido', 'estado:id_estado,nombre,tipo', 'prioridad:id_prioridad,nombre,nivel', 'area:id_area,nombre'])
                ->join('estados', 'tickets.estado_id', '=', 'estados.id_estado')
                ->where('tickets.tecnico_asignado_id', $tecnicoId)
                ->where('estados.tipo', 'abierto')
                ->orderBy('tickets.fecha_creacion', 'asc')
                ->get();

            // Tickets en proceso - OPTIMIZADO
            $tickets_proceso = \App\Models\Ticket::select('tickets.*')
                ->with(['usuario:id_usuario,nombre,apellido', 'estado:id_estado,nombre,tipo', 'prioridad:id_prioridad,nombre,nivel', 'area:id_area,nombre'])
                ->join('estados', 'tickets.estado_id', '=', 'estados.id_estado')
                ->where('tickets.tecnico_asignado_id', $tecnicoId)
                ->where('estados.tipo', 'en_proceso')
                ->orderBy('tickets.updated_at', 'desc')
                ->get();

            // Tickets resueltos (últimos 10) - OPTIMIZADO
            $tickets_resueltos = \App\Models\Ticket::select('tickets.*')
                ->with(['usuario:id_usuario,nombre,apellido', 'estado:id_estado,nombre,tipo', 'prioridad:id_prioridad,nombre,nivel', 'area:id_area,nombre'])
                ->join('estados', 'tickets.estado_id', '=', 'estados.id_estado')
                ->where('tickets.tecnico_asignado_id', $tecnicoId)
                ->where('estados.tipo', 'cerrado')
                ->orderBy('tickets.fecha_cierre', 'desc')
                ->limit(10)
                ->get();

            // Estadísticas
            $stats = [
                'pendientes' => $tickets_pendientes->count(),
                'en_proceso' => $tickets_proceso->count(),
                'resueltos'  => $tickets_resueltos->count(),
            ];

            return view('tickets.asignados', [
                'tickets_pendientes' => $tickets_pendientes,
                'tickets_proceso'    => $tickets_proceso,
                'tickets_resueltos'  => $tickets_resueltos,
                'stats'              => $stats,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al cargar tickets asignados: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Error al cargar tickets');
        }
    }

    /**
     * Obtener catálogos
     */
    private function getCatalogos()
    {
        try {
            $response = Http::withToken(session('token'))
                ->get($this->apiUrl . '/catalogos/todos');

            if ($response->successful()) {
                $data = $response->json()['data'];
                return [
                    'estados'     => $data['estados'] ?? [],
                    'prioridades' => $data['prioridades'] ?? [],
                    'areas'       => $data['areas'] ?? [],
                    'roles'       => $data['roles'] ?? [],
                ];
            }
        } catch (\Exception $e) {
            // Ignorar error
        }

        return [
            'estados'     => [],
            'prioridades' => [],
            'areas'       => [],
            'roles'       => [],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | OPTIMIZACIONES PENDIENTES
    |--------------------------------------------------------------------------
    | Se comentan para evitar errores de duplicidad de funciones.
    | Descomentar si se prefiere usar estas versiones en lugar de las de arriba.
    */

    /*
    /**
     * Optimización: Obtener catálogos con caché
     */
    /*
    private function getCatalogos()
    {
        // Cachear catálogos por 1 hora para mejor performance
        return \Illuminate\Support\Facades\Cache::remember('catalogos_tickets', 3600, function() {
            try {
                $estados = \App\Models\Estado::all();
                $prioridades = \App\Models\Prioridad::all();
                $areas = \App\Models\Area::all();
                $roles = \App\Models\Rol::all();
                
                return [
                    'estados' => $estados,
                    'prioridades' => $prioridades,
                    'areas' => $areas,
                    'roles' => $roles,
                ];
            } catch (\Exception $e) {
                \Log::error('Error al obtener catálogos: ' . $e->getMessage());
                return [
                    'estados' => collect([]),
                    'prioridades' => collect([]),
                    'areas' => collect([]),
                    'roles' => collect([]),
                ];
            }
        });
    }
    */

    /*
    /**
     * Store optimizado para usuarios
     */
    /*
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'required|string',
            'area_id' => 'required|exists:areas,id_area',
            'prioridad_id' => 'required|exists:prioridades,id_prioridad',
        ]);

        try {
            // Obtener estado "Abierto"
            $estadoAbierto = \App\Models\Estado::where('tipo', 'abierto')->first();

            $ticket = \App\Models\Ticket::create([
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'id_area' => $request->area_id,
                'id_prioridad' => $request->prioridad_id,
                'id_usuario' => session('usuario_id'),
                'id_estado' => $estadoAbierto->id_estado,
                'fecha_creacion' => now(),
            ]);

            return redirect()->route('tickets.show', $ticket->id_ticket)
                ->with('success', 'Ticket creado exitosamente');

        } catch (\Exception $e) {
            \Log::error('Error al crear ticket: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error al crear el ticket');
        }
    }
    */

    /**
     * Historial de tickets del técnico (Todos los Tickets)
     */
    public function misTicketsHistorial()
    {
        try {
            $tecnicoId = session('usuario_id');

            // Todos los tickets asignados al técnico (independiente del estado)
            $tickets = \App\Models\Ticket::with(['usuario', 'estado', 'prioridad', 'area'])
                ->where('tecnico_asignado_id', $tecnicoId)
                ->orderBy('fecha_creacion', 'desc')
                ->paginate(15);

            return view('tecnicos.historial', [
                'tickets' => $tickets,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al cargar historial de tickets: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Error al cargar historial de tickets');
        }
    }

    /**
     * Nueva Vista Estilo Figma: Ficha Técnica para el Técnico
     */
    public function verFichaTecnica($id)
    {
        try {
            // Buscamos el ticket con todas sus relaciones para que no falte ningún dato
            $ticket = \App\Models\Ticket::with([
                'usuario', 
                'area', 
                'prioridad', 
                'estado', 
                'comentarios.usuario' // Esto carga los comentarios con el nombre de quien los escribió
            ])->findOrFail($id);
            
            // Retornamos la nueva vista que crearemos en el paso 2
            return view('tecnicos.ver-ticket', compact('ticket'));
            
        } catch (\Exception $e) {
            \Log::error("Error al abrir ficha técnica: " . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo abrir la ficha técnica.');
        }
    }
}