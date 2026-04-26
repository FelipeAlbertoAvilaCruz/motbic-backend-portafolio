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
        if (! $request->user() || ! $request->user()->role) {
            return response()->json(['message' => 'No autorizado. Rol no asignado.'], 403);
        }

        if (! $request->user()->hasRole($roles)) {
            return response()->json(['message' => 'No autorizado. Permisos insuficientes.'], 403);
        }

        return $next($request);
    }
}
