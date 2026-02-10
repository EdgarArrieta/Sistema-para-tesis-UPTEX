<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Verificar que el usuario esté autenticado
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado',
            ], 401);
        }

        // Obtener el usuario con su rol
        $usuario = $request->user();
        $usuario->load('rol');

        // Verificar si el usuario tiene alguno de los roles permitidos
        foreach ($roles as $rol) {
            if ($usuario->rol->nombre === $rol) {
                return $next($request);
            }
        }

        // Si no tiene ningún rol permitido, denegar acceso
        return response()->json([
            'success' => false,
            'message' => 'No tienes permisos para acceder a este recurso',
            'required_roles' => $roles,
            'your_role' => $usuario->rol->nombre,
        ], 403);
    }
}