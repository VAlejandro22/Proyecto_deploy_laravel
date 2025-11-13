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
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        $user = auth()->user();
        
        // Si el usuario no está activo, redirigir
        if (!$user->is_active) {
            auth()->logout();
            return redirect('/')->with('error', 'Su cuenta está desactivada.');
        }

        // Verificar si el usuario tiene al menos uno de los roles especificados
        $userRoles = $user->roles->pluck('name')->toArray();
        
        foreach ($roles as $role) {
            // Permitir múltiples roles separados por |
            $allowedRoles = explode('|', $role);
            foreach ($allowedRoles as $allowedRole) {
                if (in_array(trim($allowedRole), $userRoles)) {
                    return $next($request);
                }
            }
        }

        // Si no tiene los roles necesarios, denegar acceso
        abort(403, 'No tienes permisos para acceder a esta página.');
    }

}
