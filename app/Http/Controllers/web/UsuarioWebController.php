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
     * Listar usuarios
     */
    public function index(Request $request)
    {
        try {
            $query = Usuario::with('rol');
            
            // Filtros
            if ($request->filled('id_rol')) {
                $query->where('id_rol', $request->id_rol);
            }
            
            if ($request->filled('activo')) {
                $query->where('activo', (bool)$request->activo);
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('apellido', 'like', "%{$search}%")
                      ->orWhere('correo', 'like', "%{$search}%");
                });
            }
            
            $usuarios = $query->orderBy('created_at', 'desc')->get()->map(function($usuario) {
                return [
                    'id_usuario' => $usuario->id_usuario,
                    'nombre' => $usuario->nombre,
                    'apellido' => $usuario->apellido,
                    'correo' => $usuario->correo,
                    'activo' => $usuario->activo,
                    'created_at' => $usuario->created_at,
                    'rol' => [
                        'id_rol' => $usuario->rol->id_rol,
                        'nombre' => $usuario->rol->nombre
                    ]
                ];
            });
            
            $roles = Rol::all()->map(function($rol) {
                return [
                    'id_rol' => $rol->id_rol,
                    'nombre' => $rol->nombre
                ];
            })->toArray();
            
            // Si es petición AJAX, retornar JSON
            if ($request->ajax()) {
                return response()->json([
                    'usuarios' => $usuarios->toArray()
                ]);
            }
            
            return view('usuarios.index', compact('usuarios', 'roles'));
            
        } catch (\Exception $e) {
            \Log::error('Error al cargar usuarios: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['usuarios' => []], 500);
            }
            return view('usuarios.index', ['usuarios' => collect([]), 'roles' => []])
                ->with('error', 'Error al cargar usuarios');
        }
    }
    
    /**
     * Mostrar formulario de crear usuario
     */
    public function create()
    {
        $roles = Rol::all();
        return view('usuarios.create', compact('roles'));
    }
    
    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|max:150|unique:usuarios,correo',
            'password' => 'required|string|min:6',
            'id_rol' => 'required|exists:roles,id_rol',
        ]);
        
        try {
            Usuario::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'correo' => $request->correo,
                'password' => Hash::make($request->password),
                'id_rol' => $request->id_rol,
                'activo' => $request->has('activo'),
            ]);
            
            return redirect()->route('usuarios.index')
                ->with('success', 'Usuario creado exitosamente');
                
        } catch (\Exception $e) {
            \Log::error('Error al crear usuario: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error al crear el usuario');
        }
    }
    
    /**
     * Mostrar detalle del usuario
     */
    public function show($id)
    {
        try {
            $usuario = Usuario::with(['rol', 'tickets'])->findOrFail($id);
            return view('usuarios.show', compact('usuario'));
            
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')
                ->with('error', 'Usuario no encontrado');
        }
    }
    
    /**
     * Mostrar formulario de editar
     */
    public function edit($id)
    {
        try {
            $usuario = Usuario::with('rol')->findOrFail($id);
            $roles = Rol::all();
            return view('usuarios.edit', compact('usuario', 'roles'));
            
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')
                ->with('error', 'Usuario no encontrado');
        }
    }
    
    /**
     * Actualizar usuario
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|max:150|unique:usuarios,correo,' . $id . ',id_usuario',
            'password' => 'nullable|string|min:6',
            'id_rol' => 'required|exists:roles,id_rol',
        ]);
        
        try {
            $usuario = Usuario::findOrFail($id);
            
            $usuario->nombre = $request->nombre;
            $usuario->apellido = $request->apellido;
            $usuario->correo = $request->correo;
            $usuario->id_rol = $request->id_rol;
            $usuario->activo = $request->has('activo');
            
            if ($request->filled('password')) {
                $usuario->password = Hash::make($request->password);
            }
            
            $usuario->save();
            
            return redirect()->route('usuarios.show', $id)
                ->with('success', 'Usuario actualizado exitosamente');
                
        } catch (\Exception $e) {
            \Log::error('Error al actualizar usuario: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error al actualizar el usuario');
        }
    }
    
    /**
     * Eliminar usuario
     */
    public function destroy($id)
    {
        try {
            $usuario = Usuario::findOrFail($id);
            
            // No permitir eliminar al usuario actual
            if ($id == session('usuario_id')) {
                return back()->with('error', 'No puedes eliminar tu propio usuario');
            }
            
            $usuario->delete();
            
            return redirect()->route('usuarios.index')
                ->with('success', 'Usuario eliminado exitosamente');
                
        } catch (\Exception $e) {
            \Log::error('Error al eliminar usuario: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar el usuario');
        }
    }
    
    /**
     * Activar/Desactivar usuario
     */
    public function toggleActivo($id)
    {
        try {
            $usuario = Usuario::findOrFail($id);
            $usuario->activo = !$usuario->activo;
            $usuario->save();
            
            return back()->with('success', 'Estado del usuario actualizado');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cambiar el estado');
        }
    }
}