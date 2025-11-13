<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerifiedOrAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return $next($request);
        }
        
        // Si es admin, permitir acceso sin verificación
        if ($request->user()->hasRole('Administrador')) {
            return $next($request);
        }
        
        // Para los demás usuarios, verificar email solo si no están en la ruta de verificación
        if (!$request->user()->hasVerifiedEmail()) {
            // Si ya está en la ruta de verificación, permitir acceso
            if ($request->routeIs('verification.notice') || 
                $request->routeIs('verification.verify') || 
                $request->routeIs('verification.send')) {
                return $next($request);
            }
            
            return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                : redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
