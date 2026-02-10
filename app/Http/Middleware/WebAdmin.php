<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session('usuario_rol') !== 'Administrador') {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder');
        }
        
        return $next($request);
    }
}