<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Listar todos los usuarios (solo admin)
     */
    public function index(Request $request)
    {
        if (!$request->user()->esAdministrador()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver los usuarios',
            ], 403);
        }

        $query = Usuario::with('rol');

        // Filtro por rol
        if ($request->has('id_rol')) {
            $query->where('id_rol', $request->id_rol);
        }

        // Filtro por estado activo/inactivo
        if ($request->has('activo')) {
            $query->where('activo', $request->activo);
        }

        // Búsqueda por nombre o correo
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido', 'like', "%{$search}%")
                  ->orWhere('correo', 'like', "%{$search}%");
            });
        }

        $usuarios = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $usuarios,
        ], 200);
    }

    /**
     * Crear nuevo usuario (solo admin)
     */
    public function store(Request $request)
    {
        if (!$request->user()->esAdministrador()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para crear usuarios',
            ], 403);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|max:150|unique:usuarios,correo',
            'password' => 'required|string|min:6',
            'id_rol' => 'required|exists:roles,id_rol',
            'activo' => 'sometimes|boolean',
        ]);

        $usuario = Usuario::create([
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'correo' => $validated['correo'],
            'password' => $validated['password'],
            'id_rol' => $validated['id_rol'],
            'activo' => $validated['activo'] ?? true,
        ]);

        $usuario->load('rol');

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado exitosamente',
            'data' => $usuario,
        ], 201);
    }

    /**
     * Ver detalle de un usuario (solo admin)
     */
    public function show(Request $request, $id)
    {
        if (!$request->user()->esAdministrador()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver este usuario',
            ], 403);
        }

        $usuario = Usuario::with(['rol', 'tickets', 'ticketsAsignados'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $usuario,
        ], 200);
    }

    /**
     * Actualizar usuario (solo admin)
     */
    public function update(Request $request, $id)
    {
        if (!$request->user()->esAdministrador()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para actualizar usuarios',
            ], 403);
        }

        $usuario = Usuario::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'apellido' => 'sometimes|string|max:100',
            'correo' => 'sometimes|email|max:150|unique:usuarios,correo,' . $id . ',id_usuario',
            'password' => 'sometimes|string|min:6',
            'id_rol' => 'sometimes|exists:roles,id_rol',
            'activo' => 'sometimes|boolean',
        ]);

        $usuario->update($validated);
        $usuario->load('rol');

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente',
            'data' => $usuario,
        ], 200);
    }

    /**
     * Eliminar usuario (solo admin)
     */
    public function destroy(Request $request, $id)
    {
        if (!$request->user()->esAdministrador()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para eliminar usuarios',
            ], 403);
        }

        $usuario = Usuario::findOrFail($id);

        // No permitir eliminar al propio admin
        if ($usuario->id_usuario === $request->user()->id_usuario) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes eliminar tu propio usuario',
            ], 400);
        }

        $usuario->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado exitosamente',
        ], 200);
    }

    /**
     * Activar/Desactivar usuario (solo admin)
     */
    public function toggleActivo(Request $request, $id)
    {
        if (!$request->user()->esAdministrador()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para cambiar el estado del usuario',
            ], 403);
        }

        $usuario = Usuario::findOrFail($id);
        $usuario->activo = !$usuario->activo;
        $usuario->save();
        $usuario->load('rol');

        return response()->json([
            'success' => true,
            'message' => 'Estado del usuario actualizado exitosamente',
            'data' => $usuario,
        ], 200);
    }

    /**
     * Listar técnicos (para asignación de tickets)
     */
    public function tecnicos(Request $request)
    {
        $tecnicos = Usuario::whereHas('rol', function($q) {
            $q->where('nombre', 'Técnico');
        })->where('activo', true)->get();

        return response()->json([
            'success' => true,
            'data' => $tecnicos,
        ], 200);
    }
}