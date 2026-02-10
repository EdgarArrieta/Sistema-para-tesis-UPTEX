<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebTecnico
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session('usuario_rol') !== 'Técnico') {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a este módulo');
        }
        
        return $next($request);
    }
}
