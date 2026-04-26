<?php

namespace App\Http\Controllers;

use App\Models\Traslado;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TrasladoController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventario_id' => 'required|exists:inventario,id',
            'destino_sucursal_id' => 'required|exists:sucursales,id',
            'observaciones' => 'nullable|string',
        ]);
        
        $inventario = Inventario::findOrFail($validated['inventario_id']);

        if (!$inventario->sucursal_id) {
             return response()->json(['error' => 'El inventario no está asignado a ninguna sucursal.'], 400);
        }

        if ($inventario->sucursal_id == $validated['destino_sucursal_id']) {
             return response()->json(['error' => 'La sucursal de destino es la misma que la de origen.'], 400);
        }

        DB::transaction(function () use ($validated, $inventario, $request) {
            $origen = $inventario->sucursal_id;
            
            // Crear registro de traslado
            Traslado::create([
                'inventario_id' => $inventario->id,
                'origen_sucursal_id' => $origen,
                'destino_sucursal_id' => $validated['destino_sucursal_id'],
                'user_id' => $request->user() ? $request->user()->id : 1, // Fallback if no user
                'fecha_traslado' => now(),
                'estado' => 'completado',
                'observaciones' => $request->input('observaciones'),
            ]);

            // Actualizar inventario
            $inventario->update(['sucursal_id' => $validated['destino_sucursal_id']]);
        });

        return response()->json(['message' => 'Traslado realizado con éxito'], 201);
    }

    public function index(Request $request)
    {
        $query = Traslado::with(['inventario.modelo', 'sucursalOrigen', 'sucursalDestino', 'user']);

        // Filtros opcionales
        if ($request->has('sucursal_id')) {
            $sucursalId = $request->input('sucursal_id');
            $query->where(function ($q) use ($sucursalId) {
                $q->where('origen_sucursal_id', $sucursalId)
                  ->orWhere('destino_sucursal_id', $sucursalId);
            });
        }

        // Filtro por rango de fechas (Entre fecha A y fecha B)
        if ($request->filled(['fecha_inicio', 'fecha_fin'])) {
            $start = Carbon::parse($request->input('fecha_inicio'))->startOfDay();
            $end = Carbon::parse($request->input('fecha_fin'))->endOfDay();
            
            $query->whereBetween('fecha_traslado', [$start, $end]);
        } elseif ($request->has('fecha_inicio')) {
            // Solo fecha inicio
            $query->whereDate('fecha_traslado', '>=', $request->input('fecha_inicio'));
        } elseif ($request->has('fecha_fin')) {
            // Solo fecha fin
            $query->whereDate('fecha_traslado', '<=', $request->input('fecha_fin'));
        }
        
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        return response()->json($query->latest()->paginate(20));
    }
}
