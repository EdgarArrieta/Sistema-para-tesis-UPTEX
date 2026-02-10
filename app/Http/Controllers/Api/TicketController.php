<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Estado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Listar tickets (con filtros)
     */
    public function index(Request $request)
    {
        $usuario = $request->user();
        $query = Ticket::with(['usuario', 'area', 'prioridad', 'estado', 'tecnicoAsignado']);

        // Filtrar según el rol
        if ($usuario->esUsuarioNormal()) {
            // Usuario normal solo ve sus tickets
            $query->where('usuario_id', $usuario->id_usuario);
        } elseif ($usuario->esTecnico()) {
            // Técnico ve tickets asignados a él
            $query->where('tecnico_asignado_id', $usuario->id_usuario);
        }
        // Admin ve todos los tickets

        // Filtros opcionales
        if ($request->has('estado_id')) {
            $query->where('estado_id', $request->estado_id);
        }

        if ($request->has('prioridad_id')) {
            $query->where('prioridad_id', $request->prioridad_id);
        }

        if ($request->has('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->has('tecnico_asignado_id')) {
            $query->where('tecnico_asignado_id', $request->tecnico_asignado_id);
        }

        // Búsqueda por título o descripción
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        // Ordenar
        $query->orderBy('fecha_creacion', 'desc');

        // Paginar
        $tickets = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $tickets,
        ], 200);
    }

    /**
     * Crear nuevo ticket
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'required|string',
            'area_id' => 'required|exists:areas,id_area',
            'prioridad_id' => 'required|exists:prioridades,id_prioridad',
        ]);

        $usuario = $request->user();

        // Estado inicial: Abierto (id = 1)
        $estadoAbierto = Estado::where('tipo', Estado::TIPO_ABIERTO)->first();

        $ticket = Ticket::create([
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'],
            'usuario_id' => $usuario->id_usuario,
            'area_id' => $validated['area_id'],
            'prioridad_id' => $validated['prioridad_id'],
            'estado_id' => $estadoAbierto->id_estado,
        ]);

        $ticket->load(['usuario', 'area', 'prioridad', 'estado']);

        return response()->json([
            'success' => true,
            'message' => 'Ticket creado exitosamente',
            'data' => $ticket,
        ], 201);
    }

    /**
     * Ver detalle de un ticket
     */
    public function show(Request $request, $id)
    {
        $usuario = $request->user();
        $ticket = Ticket::with(['usuario', 'area', 'prioridad', 'estado', 'tecnicoAsignado', 'comentarios.usuario'])
            ->findOrFail($id);

        // Verificar permisos
        if ($usuario->esUsuarioNormal() && $ticket->usuario_id !== $usuario->id_usuario) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver este ticket',
            ], 403);
        }

        if ($usuario->esTecnico() && $ticket->tecnico_asignado_id !== $usuario->id_usuario) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver este ticket',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $ticket,
        ], 200);
    }

    /**
     * Actualizar ticket
     */
    public function update(Request $request, $id)
    {
        $usuario = $request->user();
        $ticket = Ticket::findOrFail($id);

        // Solo admin o técnico asignado pueden actualizar
        if (!$usuario->esAdministrador() && $ticket->tecnico_asignado_id !== $usuario->id_usuario) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para actualizar este ticket',
            ], 403);
        }

        $validated = $request->validate([
            'titulo' => 'sometimes|string|max:200',
            'descripcion' => 'sometimes|string',
            'area_id' => 'sometimes|exists:areas,id_area',
            'prioridad_id' => 'sometimes|exists:prioridades,id_prioridad',
            'estado_id' => 'sometimes|exists:estados,id_estado',
        ]);

        $ticket->update($validated);
        $ticket->load(['usuario', 'area', 'prioridad', 'estado', 'tecnicoAsignado']);

        return response()->json([
            'success' => true,
            'message' => 'Ticket actualizado exitosamente',
            'data' => $ticket,
        ], 200);
    }

    /**
     * Eliminar ticket (soft delete)
     */
    public function destroy(Request $request, $id)
    {
        $usuario = $request->user();

        if (!$usuario->esAdministrador()) {
            return response()->json([
                'success' => false,
                'message' => 'Solo los administradores pueden eliminar tickets',
            ], 403);
        }

        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ticket eliminado exitosamente',
        ], 200);
    }

    /**
     * Asignar técnico a un ticket
     */
    public function asignarTecnico(Request $request, $id)
    {
        $usuario = $request->user();

        if (!$usuario->esAdministrador()) {
            return response()->json([
                'success' => false,
                'message' => 'Solo los administradores pueden asignar técnicos',
            ], 403);
        }

        $validated = $request->validate([
            'tecnico_id' => 'required|exists:usuarios,id_usuario',
        ]);

        $ticket = Ticket::findOrFail($id);
        $ticket->asignarTecnico($validated['tecnico_id']);
        $ticket->load(['usuario', 'area', 'prioridad', 'estado', 'tecnicoAsignado']);

        return response()->json([
            'success' => true,
            'message' => 'Técnico asignado exitosamente',
            'data' => $ticket,
        ], 200);
    }

    /**
     * Cambiar estado del ticket
     */
    public function cambiarEstado(Request $request, $id)
    {
        $usuario = $request->user();
        $ticket = Ticket::findOrFail($id);

        // Solo admin o técnico asignado
        if (!$usuario->esAdministrador() && $ticket->tecnico_asignado_id !== $usuario->id_usuario) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para cambiar el estado',
            ], 403);
        }

        $validated = $request->validate([
            'estado_id' => 'required|exists:estados,id_estado',
        ]);

        $ticket->estado_id = $validated['estado_id'];
        $ticket->save();
        $ticket->load(['usuario', 'area', 'prioridad', 'estado', 'tecnicoAsignado']);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado exitosamente',
            'data' => $ticket,
        ], 200);
    }

    /**
     * Cerrar ticket con solución
     */
    public function cerrar(Request $request, $id)
    {
        $usuario = $request->user();
        $ticket = Ticket::findOrFail($id);

        // Solo admin o técnico asignado
        if (!$usuario->esAdministrador() && $ticket->tecnico_asignado_id !== $usuario->id_usuario) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para cerrar este ticket',
            ], 403);
        }

        $validated = $request->validate([
            'solucion' => 'required|string',
        ]);

        $ticket->cerrar($validated['solucion']);
        $ticket->load(['usuario', 'area', 'prioridad', 'estado', 'tecnicoAsignado']);

        return response()->json([
            'success' => true,
            'message' => 'Ticket cerrado exitosamente',
            'data' => $ticket,
        ], 200);
    }
}