<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\TrasladoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Auth Public
Route::post('/auth/login', [AuthController::class, 'login']);

// Users (Public)
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'get']);
Route::post('/users', [UserController::class, 'save']);
Route::patch('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'delete']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Roles (Solo Admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('/roles', [RoleController::class, 'index']);
        Route::post('/roles', [RoleController::class, 'save']);
        Route::get('/roles/{id}', [RoleController::class, 'get']);
        Route::patch('/roles/{id}', [RoleController::class, 'update']);
        Route::delete('/roles/{id}', [RoleController::class, 'delete']);
    });

    // Modelos
    Route::middleware('role:admin,gerente,empleado')->group(function () {
        Route::get('/modelos', [ModeloController::class, 'get']);
        Route::get('/modelos/search', [ModeloController::class, 'search']);
        Route::post('/modelos', [ModeloController::class, 'save']);
        Route::patch('/modelos/{id}', [ModeloController::class, 'update']);
        Route::delete('/modelos/{id}', [ModeloController::class, 'delete']);
    });

    // Inventario
    Route::middleware('role:admin,gerente,empleado')->group(function () {
        Route::get('/inventario/conteo', [InventarioController::class, 'counter']);
        Route::get('/inventario/search', [InventarioController::class, 'search']);
        Route::get('/inventario', [InventarioController::class, 'get']);
        Route::post('/inventario', [InventarioController::class, 'save']);
    });

    // Clientes
    Route::middleware('role:admin,gerente,empleado')->group(function () {
        Route::get('/clientes', [ClienteController::class, 'get_all']);
        Route::get('/clientes/search', [ClienteController::class, 'search']);
        Route::get('/clientes/{id}', [ClienteController::class, 'get']);
        Route::post('/clientes', [ClienteController::class, 'save']);
        Route::patch('/clientes/{id}', [ClienteController::class, 'update']);
        Route::delete('/clientes/{id}', [ClienteController::class, 'delete']);
    });

    // Ventas
    Route::middleware('role:admin,gerente,empleado')->group(function () {
        Route::get('/ventas', [VentaController::class, 'get']);
        Route::post('/ventas', [VentaController::class, 'save']);
        Route::get('/ventas/{id}', [VentaController::class, 'show']);
    });

    // Services
    Route::middleware('role:admin,gerente,empleado')->group(function () {
        Route::get('/servicios', [ServicioController::class, 'search']);
        Route::get('/servicios/conteo', [ServicioController::class, 'counter']);
        Route::get('/servicios/cliente_id/{cliente_id}', [ServicioController::class, 'get_by_cliente_id']);
        Route::get('/servicios/get_by_motocicleta/{get_by_motocicleta_id}', [ServicioController::class, 'get_by_motocicleta']);
        Route::get('/servicios/get_by_motocicleta_externa', [ServicioController::class, 'get_by_moto_externa']);
        Route::get('/servicios/{id}', [ServicioController::class, 'get']);
        Route::post('/servicios', [ServicioController::class, 'save']);
        Route::patch('/servicios/{id}/completar', [ServicioController::class, 'complete']);
        Route::delete('/servicios/{id}', [ServicioController::class, 'delete']);
    });

    // Sucursales
    Route::middleware('role:admin,gerente')->group(function () {
        Route::get('/sucursales', [SucursalController::class, 'index']);
        Route::post('/sucursales', [SucursalController::class, 'store']);
        Route::get('/sucursales/{id}', [SucursalController::class, 'show']);
        Route::put('/sucursales/{id}', [SucursalController::class, 'update']);
        Route::delete('/sucursales/{id}', [SucursalController::class, 'destroy']);
        Route::get('/sucursales/{id}/users', [SucursalController::class, 'getUsers']);
    });

    // Traslados
    Route::middleware('role:admin,gerente,empleado')->group(function () {
        Route::get('/traslados', [TrasladoController::class, 'index']);
        Route::post('/traslados', [TrasladoController::class, 'store']);
    });
});
