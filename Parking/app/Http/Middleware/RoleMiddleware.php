<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class RoleMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
{
    \Log::info('Middleware RoleMiddleware ejecutado', [
        'user' => Auth::user(),
        'roles' => $roles,
    ]);

    if (Auth::check() && in_array(Auth::user()->role, $roles)) {
        return $next($request);
    }

    abort(403, 'No tienes permisos para realizar esta acción.');
}
}
