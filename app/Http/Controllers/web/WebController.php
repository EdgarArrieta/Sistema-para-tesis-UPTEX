<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\Estado;
use App\Models\Prioridad;
use App\Models\Area;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WebController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        if (session('token')) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }
    
    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required',
        ]);
        
        try {
            // Buscar usuario
            $usuario = Usuario::with('rol')
                ->where('correo', $request->correo)
                ->where('activo', true)
                ->first();
            
            if (!$usuario || !Hash::check($request->password, $usuario->password)) {
                return back()->withErrors(['correo' => 'Credenciales incorrectas'])->withInput();
            }
            
            // Crear token simulado
            $token = bin2hex(random_bytes(32));
            
            // Guardar en sesión
            session([
                'token' => $token,
                'usuario_id' => $usuario->id_usuario,
                'usuario_nombre' => $usuario->nombre_completo,
                'usuario_rol' => $usuario->rol->nombre,
                'usuario_objeto' => $usuario,
            ]);
            
            return redirect()->route('dashboard')->with('success', 'Bienvenido al sistema');
            
        } catch (\Exception $e) {
            \Log::error('Error en login: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al procesar el login'])->withInput();
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente');
    }
    
    /**
     * Dashboard según rol
     */
    public function dashboard()
    {
        try {
            $rol = session('usuario_rol');
            $usuarioId = session('usuario_id');
            
            // Si no hay sesión válida, redirigir al login
            if (!$rol || !$usuarioId) {
                return redirect()->route('login')->with('error', 'Sesión expirada');
            }
            
            // DASHBOARD PARA USUARIO NORMAL
            if ($rol === 'Usuario Normal') {
                // Obtener tickets del usuario con relaciones (últimos 10)
                $tickets = \App\Models\Ticket::where('usuario_id', $usuarioId)
                    ->with(['usuario', 'area', 'prioridad', 'estado', 'tecnicoAsignado'])
                    ->orderBy('fecha_creacion', 'desc')
                    ->limit(10)
                    ->get();
                
                // Calcular estadísticas
                $stats = [
                    'total' => \App\Models\Ticket::where('usuario_id', $usuarioId)->count(),
                    'abiertos' => \App\Models\Ticket::where('usuario_id', $usuarioId)
                        ->whereHas('estado', function($q) {
                            $q->whereIn('tipo', ['abierto', 'en_proceso']);
                        })->count(),
                    'cerrados' => \App\Models\Ticket::where('usuario_id', $usuarioId)
                        ->whereHas('estado', function($q) {
                            $q->where('tipo', 'cerrado');
                        })->count(),
                ];
                
                return view('usuarios.dashboard', [
                    'tickets' => $tickets,
                    'stats' => $stats,
                ]);
            }
            
            // DASHBOARD PARA TÉCNICO
            if ($rol === 'Técnico') {
                // Tickets asignados al técnico
                $ticketsAsignados = \App\Models\Ticket::with(['usuario', 'area', 'prioridad', 'estado'])
                    ->where('tecnico_asignado_id', $usuarioId)
                    ->whereHas('estado', function ($q) {
                        $q->whereIn('tipo', ['abierto', 'en_proceso']);
                    })
                    ->orderBy('prioridad_id', 'desc')
                    ->orderBy('fecha_creacion', 'asc')
                    ->limit(5)
                    ->get();

                // Contadores para estadísticas
                $totalAsignados = \App\Models\Ticket::where('tecnico_asignado_id', $usuarioId)
                    ->whereHas('estado', function ($q) {
                        $q->whereIn('tipo', ['abierto', 'en_proceso', 'pendiente']);
                    })
                    ->count();

                $enProceso = \App\Models\Ticket::where('tecnico_asignado_id', $usuarioId)
                    ->whereHas('estado', function ($q) {
                        $q->where('tipo', 'en_proceso');
                    })
                    ->count();

                $resueltosHoy = \App\Models\Ticket::where('tecnico_asignado_id', $usuarioId)
                    ->whereHas('estado', function ($q) {
                        $q->where('tipo', 'cerrado');
                    })
                    ->whereDate('fecha_cierre', \Carbon\Carbon::today())
                    ->count();

                $urgentes = \App\Models\Ticket::where('tecnico_asignado_id', $usuarioId)
                    ->whereHas('prioridad', function ($q) {
                        $q->where('nivel', '>=', 3);
                    })
                    ->whereHas('estado', function ($q) {
                        $q->whereIn('tipo', ['abierto', 'en_proceso']);
                    })
                    ->count();

                return view('tecnicos.dashboard', [
                    'tickets' => $ticketsAsignados,
                    'stats' => [
                        'asignados' => $totalAsignados,
                        'en_proceso' => $enProceso,
                        'resueltos_hoy' => $resueltosHoy,
                        'urgentes' => $urgentes,
                    ],
                ]);
            }
            
            // DASHBOARD PARA ADMINISTRADOR
            if ($rol === 'Administrador') {
                // Contar total de usuarios activos
                $total_usuarios = \App\Models\Usuario::where('activo', true)->count();
                
                // Contar técnicos (usuarios con rol de Técnico)
                $tecnicos = \App\Models\Usuario::whereHas('rol', function($query) {
                    $query->where('nombre', 'Técnico');
                })->where('activo', true)->count();
                
                // Contar total de tickets
                $total_tickets = \App\Models\Ticket::count();
                
                // Contar tickets cerrados
                $tickets_cerrados = \App\Models\Ticket::whereHas('estado', function($query) {
                    $query->where('tipo', 'cerrado');
                })->count();
                
                return view('admin.dashboard', [
                    'stats' => [
                        'total_usuarios' => $total_usuarios,
                        'tecnicos' => $tecnicos,
                        'total_tickets' => $total_tickets,
                        'tickets_cerrados' => $tickets_cerrados,
                    ],
                    'actividad' => [],
                ]);
            }
            
            // Si el rol no es reconocido
            return redirect()->route('login')->with('error', 'Rol no reconocido');
            
        } catch (\Exception $e) {
            \Log::error('Error en dashboard: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->route('login')->with('error', 'Error al cargar dashboard');
        }
    }
           
    /**
     * Ver perfil
     */
    public function perfil()
    {
        try {
            $usuario = Usuario::with('rol')->find(session('usuario_id'));
            
            if (!$usuario) {
                return redirect()->route('dashboard')->with('error', 'Usuario no encontrado');
            }
            
            return view('perfil', compact('usuario'));
            
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Error al cargar perfil');
        }
    }
    
    /**
     * Actualizar perfil
     */
    public function updatePerfil(Request $request)
    {
        $request->validate([
            'nombre' => 'nullable|string|max:100',
            'apellido' => 'nullable|string|max:100',
            'correo' => 'nullable|email|max:150',
            'password' => 'nullable|string|min:6|confirmed',
        ]);
        
        try {
            $usuario = Usuario::find(session('usuario_id'));
            
            if (!$usuario) {
                return back()->with('error', 'Usuario no encontrado');
            }
            
            if ($request->filled('nombre')) {
                $usuario->nombre = $request->nombre;
            }
            
            if ($request->filled('apellido')) {
                $usuario->apellido = $request->apellido;
            }
            
            if ($request->filled('correo')) {
                $usuario->correo = $request->correo;
            }
            
            if ($request->filled('password')) {
                $usuario->password = Hash::make($request->password);
            }
            
            $usuario->save();
            
            // Actualizar sesión
            session(['usuario_nombre' => $usuario->nombre_completo]);
            
            return back()->with('success', 'Perfil actualizado exitosamente');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar perfil');
        }
    }


    /**
     * Mostrar formulario de registro
     */
    public function showRegister()
    {
        return view('auth.register');
    }
    
    /**
     * Procesar registro de usuario normal
     */
    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|max:150|unique:usuarios,correo',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'correo.unique' => 'Este correo ya está registrado',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
        ]);
        
        try {
            // Buscar el rol de "Usuario Normal"
            $rolUsuario = \App\Models\Rol::where('nombre', 'Usuario Normal')->first();
            
            if (!$rolUsuario) {
                return back()->withErrors(['error' => 'Error en la configuración del sistema'])->withInput();
            }
            
            // Crear usuario
            $usuario = \App\Models\Usuario::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'correo' => $request->correo,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'id_rol' => $rolUsuario->id_rol,
                'activo' => true,
            ]);
            
            return redirect()->route('login')->with('success', 'Cuenta creada exitosamente. Ya puedes iniciar sesión.');
            
        } catch (\Exception $e) {
            \Log::error('Error en registro: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al crear la cuenta. Intenta nuevamente.'])->withInput();
        }
    }



}