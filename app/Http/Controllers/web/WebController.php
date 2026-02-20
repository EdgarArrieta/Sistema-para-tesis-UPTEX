<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Ticket;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class WebController extends Controller
{
    public function showLogin() { if (session('token')) { return redirect()->route('dashboard'); } return view('auth.login'); }
    
    public function login(Request $request) {
        $request->validate(['correo' => 'required|email', 'password' => 'required']);
        try {
            $usuario = Usuario::with('rol')->where('correo', $request->correo)->where('activo', true)->first();
            if (!$usuario || !Hash::check($request->password, $usuario->password)) {
                return back()->withErrors(['correo' => 'Credenciales incorrectas'])->withInput();
            }
            // Actualizar último acceso al login con zona horaria de México
            $usuario->update(['last_login' => \Carbon\Carbon::now('America/Mexico_City')]);
            session(['token' => bin2hex(random_bytes(32)), 'usuario_id' => $usuario->id_usuario, 'usuario_nombre' => $usuario->nombre_completo, 'usuario_rol' => $usuario->rol->nombre]);
            return redirect()->route('dashboard');
        } catch (\Exception $e) { return back()->withErrors(['error' => 'Error al entrar']); }
    }

    /**
     * DASHBOARD: Redirección al Panel de Trabajo correspondiente
     */
    public function dashboard() {
        $rol = session('usuario_rol');
        $usuarioId = session('usuario_id');
        if (!$rol) return redirect()->route('login');

        // 1. Administrador (Sin cambios)
        if ($rol === 'Administrador') return app(ReporteWebController::class)->panelAdmin();

        // 2. TÉCNICO: Redirige a la función que acabamos de arreglar para cargar el Panel de Trabajo
        if ($rol === 'Técnico') return redirect()->route('tickets.asignados');

        // 3. Usuario Normal (Estadísticas del Usuario)
        $stats = [
            'total' => Ticket::where('usuario_id', $usuarioId)->count(),
            'en_proceso' => Ticket::where('usuario_id', $usuarioId)
                ->whereHas('estado', function($q) { $q->whereIn('tipo', ['abierto', 'en_proceso', 'pendiente']); })->count(),
            'resueltos' => Ticket::where('usuario_id', $usuarioId)
                ->whereHas('estado', function($q) { $q->where('tipo', 'resuelto'); })->count(),
        ];

        $tickets = Ticket::where('usuario_id', $usuarioId)->with(['estado', 'prioridad', 'area'])->orderBy('fecha_creacion', 'desc')->limit(5)->get();

        return view('usuarios.dashboard', compact('stats', 'tickets'));
    }

    public function logout(Request $request) { $request->session()->flush(); return redirect()->route('login'); }
    public function showRegister() { return view('auth.register'); }
    public function register(Request $request) { return redirect()->route('login'); }
    public function perfil() { $usuario = Usuario::with('rol')->find(session('usuario_id'))->toArray(); return view('perfil', compact('usuario')); }
    
    public function updatePerfil(Request $request) { 
        $usuario = Usuario::find(session('usuario_id'));
        
        // Validar
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'correo' => 'required|email|unique:usuarios,correo,' . $usuario->id_usuario . ',id_usuario',
            'password' => 'nullable|min:8|confirmed',
        ]);
        
        // Actualizar datos básicos
        $usuario->update([
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'correo' => $validated['correo'],
        ]);
        
        // Si se proporciona contraseña, actualizar
        if (!empty($validated['password'])) {
            $usuario->update(['password' => Hash::make($validated['password'])]);
        }
        
        return back()->with('success', 'Perfil actualizado correctamente');
    }
}