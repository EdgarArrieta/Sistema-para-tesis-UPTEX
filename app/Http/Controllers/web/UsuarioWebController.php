<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;

class UsuarioWebController extends Controller
{
    /**
     * Lista de usuarios (Se mantiene intacta)
     */
    public function index()
    {
        $usuarios = Usuario::with('rol')->orderBy('nombre')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * EL BOTÓN AZUL (EL OJO): Ver detalle del usuario (Se mantiene intacta)
     */
    public function show($id)
    {
        try {
            $u = Usuario::with(['rol', 'tickets.estado', 'ticketsAsignados.estado'])->findOrFail($id);

            $usuario = [
                'id_usuario' => $u->id_usuario,
                'nombre'     => $u->nombre,
                'apellido'   => $u->apellido,
                'correo'     => $u->correo,
                'activo'     => $u->activo,
                'created_at' => $u->created_at,
                'updated_at' => $u->updated_at,
                'rol' => [
                    'nombre' => $u->rol->nombre ?? 'N/A'
                ],
                'tickets' => $u->tickets->map(function($t) {
                    return [
                        'id_ticket'      => $t->id_ticket,
                        'titulo'         => $t->titulo,
                        'fecha_creacion' => $t->fecha_creacion,
                        'estado' => [
                            'nombre' => $t->estado->nombre ?? 'N/A',
                            'tipo'   => $t->estado->tipo ?? 'abierto'
                        ]
                    ];
                })->toArray(),
                'tickets_asignados' => $u->ticketsAsignados ?? []
            ];

            return view('usuarios.show', compact('usuario'));

        } catch (\Exception $e) {
            \Log::error("Error al mostrar usuario: " . $e->getMessage());
            return redirect()->route('usuarios.index')->with('error', 'No se pudo cargar la información.');
        }
    }

    /**
     * EL LÁPIZ: Formulario de edición (Se mantiene intacta)
     */
    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);
        $roles = Rol::all();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    /**
     * Procesar actualización (Se mantiene intacta)
     */
    public function update(Request $request, $id)
    {
        $u = Usuario::findOrFail($id);
        $u->update($request->except('password'));
        if ($request->filled('password')) {
            $u->password = Hash::make($request->password);
            $u->save();
        }
        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Botón de activar/desactivar (Se mantiene intacta)
     */
    public function toggleActivo($id)
    {
        $u = Usuario::findOrFail($id);
        $u->activo = !$u->activo;
        $u->save();
        return back()->with('success', 'Estado de usuario actualizado.');
    }

    /**
     * EL BOTÓN ROJO (BASURA): Eliminar usuario
     */
    public function destroy($id)
    {
        try {
            // Buscar el usuario a eliminar
            $usuario = Usuario::find($id);
            
            if (!$usuario) {
                return back()->with('error', 'El usuario no existe.');
            }
            
            // Obtener el ID del usuario actual desde la sesión
            $usuarioActualId = session('usuario_id');
            
            // No permitir eliminar al usuario actual
            if ($usuarioActualId && $usuarioActualId == $id) {
                return back()->with('error', 'No puedes eliminar tu propio usuario.');
            }
            
            // Eliminar comentarios asociados
            $usuario->comentarios()->delete();
            
            // Desvincular tickets asignados
            $usuario->ticketsAsignados()->update(['tecnico_asignado_id' => null]);
            
            // Eliminar tickets creados por el usuario
            $usuario->tickets()->delete();
            
            // Eliminar el usuario
            $usuario->delete();
            
            return back()->with('success', 'Usuario eliminado correctamente.');
            
        } catch (\Exception $e) {
            \Log::error("Error al eliminar usuario: " . $e->getMessage() . " | ID: " . $id);
            return back()->with('error', 'Error al eliminar el usuario.');
        }
    }

    // --- LAS SIGUIENTES 4 FUNCIONES SON LAS QUE ARREGLAN LOS BOTONES DE CREACIÓN ---

    /**
     * Vista para crear Usuario Normal
     */
    public function createUsuario()
    {
        return view('usuarios.create');
    }

    /**
     * Guardar Usuario Normal (Rol ID 3)
     */
    public function storeUsuario(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'correo' => 'required|email|unique:usuarios,correo',
            'password' => 'required|min:6|confirmed',
        ]);

        Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'correo' => $request->correo,
            'password' => Hash::make($request->password),
            'id_rol' => 3, // Usuario Normal
            'activo' => true,
        ]);

        return redirect()->route('dashboard')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Vista para crear Técnico
     */
    public function createTecnico()
    {
        return view('tecnicos.create');
    }

    /**
     * Guardar Técnico (Rol ID 2)
     */
    public function storeTecnico(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'correo' => 'required|email|unique:usuarios,correo',
            'password' => 'required|min:6|confirmed',
        ]);

        Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'correo' => $request->correo,
            'password' => Hash::make($request->password),
            'id_rol' => 2, // Técnico
            'activo' => true,
        ]);

        return redirect()->route('dashboard')->with('success', 'Técnico creado correctamente.');
    }
}