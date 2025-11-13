<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActive
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (mixed)  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Si no hay usuario autenticado o está inactivo, cerrar sesión y redirigir
        if (! $request->user() || ! $request->user()->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors('Su sesión ha finalizado, contacte con el administrador.');
        }

        return $next($request);
    }
}
