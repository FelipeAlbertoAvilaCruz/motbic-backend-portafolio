<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Inventario;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    public function get(Request $request)
    {
        // Query base con relaciones y orden
        $query = Venta::with(['cliente', 'inventario.modelo'])
            ->orderBy('created_at', 'desc');

        // Filtro obligatorio de sucursal
        if ($request->has('sucursal_id')) {
            $query->where('sucursal_id', $request->sucursal_id);
        }

        // Contadores por estado (sólo filtro de sucursal, sin búsqueda)
        $statsQuery = Venta::query();
        if ($request->has('sucursal_id')) {
            $statsQuery->where('sucursal_id', $request->sucursal_id);
        }
        $completadasTotal = (clone $statsQuery)->where('estado', 'completada')->count();
        $pendientesTotal  = (clone $statsQuery)->where('estado', 'pendiente')->count();

        // Filtros de búsqueda (afectan la lista paginada, no los contadores)
        if ($request->filled('folio')) {
            $query->where('folio', 'like', '%' . $request->folio . '%');
        }

        if ($request->filled('cliente')) {
            $search = $request->cliente;
            $query->whereHas('cliente', function ($q) use ($search) {
                $q->whereRaw("CONCAT(nombres, ' ', apellidos) LIKE ?", ["%{$search}%"]);
            });
        }

        $ventas = $query->paginate(10);
        return response()->json([
            'results'           => $ventas->items(),
            'count'             => $ventas->total(),
            'current_page'      => $ventas->currentPage(),
            'last_page'         => $ventas->lastPage(),
            'completadas_total' => $completadasTotal,
            'pendientes_total'  => $pendientesTotal,
        ]);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'inventario_id' => 'required|exists:inventario,id',
            'fecha' => 'required|date',
            'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta-credito,tarjeta-debito,cheque,financiamiento,mixto',
            'precio_total' => 'required|numeric',
            'estado' => 'in:completada,pendiente,cancelada',
        ]);

        // Verificar que la unidad esté disponible
        $inventario = Inventario::findOrFail($validated['inventario_id']);
        if ($inventario->estado !== 'disponible') {
            return response()->json([
                'message' => 'La unidad de inventario no está disponible para la venta.',
                'estado_actual' => $inventario->estado,
            ], 422);
        }

        // Generate Folio
        $lastVenta = Venta::latest()->first();
        $nextId = $lastVenta ? intval(substr($lastVenta->folio, 2)) + 1 : 1;
        $folio = 'V-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $venta = Venta::create(array_merge($validated, ['folio' => $folio]));

        // Marcar la unidad de inventario como vendida
        Inventario::where('id', $validated['inventario_id'])
            ->update(['estado' => 'vendida']);

        Cliente::where('id', $validated['cliente_id'])
        ->update([
            'ultima_compra' => $validated['fecha']
        ]);

        // Load relationships for response
        $venta->load(['cliente', 'inventario.modelo']);

        // Format response to match docs
        $response = $venta->toArray();
        if ($venta->inventario && $venta->inventario->modelo) {
             $response['motocicleta'] = $venta->inventario->modelo->marca . ' ' . $venta->inventario->modelo->nombre;
        }
        if ($venta->cliente) {
            $response['cliente'] = $venta->cliente->nombres . ' ' . $venta->cliente->apellidos;
        }

        return response()->json($response, 201);
    }

    public function show($id)
    {
        $venta = Venta::with(['cliente', 'inventario.modelo'])->findOrFail($id);
        return response()->json($venta);
    }
}
