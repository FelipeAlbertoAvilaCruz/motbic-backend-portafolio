<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \App\Http\Middleware\ForceJsonResponse::class,
        ]);
        
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Forzar respuesta JSON para rutas API
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }
            return $request->expectsJson();
        });

        // 401: No autenticado
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'No autenticado. Token inválido o expirado.'], 401);
            }
        });

        // 404: Recurso no encontrado (ModelNotFound)
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['error' => 'Recurso no encontrado'], 404);
            }
        });

        // 406: Validación fallida (Personalizado, por defecto es 422)
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['error' => 'Datos no válidos', 'details' => $e->errors()], 406);
            }
        });

        // 500: Error general (Opcional, para capturar todo lo demás en JSON)
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'error' => 'Error interno del servidor',
                    'message' => $e->getMessage()
                ], 500);
            }
        });
    })->create();
