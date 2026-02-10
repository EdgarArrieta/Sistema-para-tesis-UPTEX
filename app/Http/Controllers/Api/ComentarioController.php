<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Comentario;
use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    /**
     * Listar comentarios de un ticket
     */
    public function index($ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        $comentarios = $ticket->comentarios()->with('usuario')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $comentarios,
        ], 200);
    }

    /**
     * Agregar comentario a un ticket
     */
    public function store(Request $request, $ticketId)
    {
        $usuario = $request->user();
        $ticket = Ticket::findOrFail($ticketId);

        // Verificar permisos
        if ($usuario->esUsuarioNormal() && $ticket->usuario_id !== $usuario->id_usuario) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para comentar en este ticket',
            ], 403);
        }

        if ($usuario->esTecnico() && $ticket->tecnico_asignado_id !== $usuario->id_usuario) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para comentar en este ticket',
            ], 403);
        }

        $validated = $request->validate([
            'contenido' => 'required|string',
        ]);

        $comentario = Comentario::create([
            'ticket_id' => $ticketId,
            'usuario_id' => $usuario->id_usuario,
            'contenido' => $validated['contenido'],
        ]);

        $comentario->load('usuario');

        return response()->json([
            'success' => true,
            'message' => 'Comentario agregado exitosamente',
            'data' => $comentario,
        ], 201);
    }
}