<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('token')) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión');
        }
        
        return $next($request);
    }
}