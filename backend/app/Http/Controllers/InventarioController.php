<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function counter(Request $request)
    {
        $query = Inventario::query();

        if ($request->has('sucursal_id')) {
            $ids = $request->sucursal_id;
            if (is_string($ids)) {
                $ids = array_filter(array_map('trim', explode(',', $ids)));
            }
            $query->whereIn('sucursal_id', (array) $ids);
        }

        $total       = (clone $query)->count();
        $disponibles = (clone $query)->where('estado', 'disponible')->count();
        $vendidas    = (clone $query)->where('estado', 'vendida')->count();
        $defectuosas = (clone $query)->where('estado', 'defectuosa')->count();

        return response()->json([
            'total'       => $total,
            'disponibles' => $disponibles,
            'vendidas'    => $vendidas,
            'defectuosas' => $defectuosas,
        ]);
    }

    public function get(Request $request)
    {
        $query = Inventario::with('modelo');

        if ($request->has('sucursal_id')) {
            $ids = $request->sucursal_id;
            if (is_string($ids)) {
                $ids = array_filter(array_map('trim', explode(',', $ids)));
            }
            $query->whereIn('sucursal_id', (array) $ids);
        }

        if ($request->has('exclude_sucursal_id')) {
            $query->where('sucursal_id', '!=', $request->exclude_sucursal_id);
        }

        if ($request->has('modelo_id')) {
            $query->where('modelo_id', $request->modelo_id);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('serie')) {
            $query->where('serie', 'like', '%' . $request->serie . '%');
        }

        if ($request->filled('nombre') || $request->filled('marca')) {
            $query->whereHas('modelo', function ($q) use ($request) {
                if ($request->filled('nombre')) {
                    $q->where('nombre', 'like', '%' . $request->nombre . '%');
                }
                if ($request->filled('marca')) {
                    $q->where('marca', 'like', '%' . $request->marca . '%');
                }
            });
        }

        $inventario = $query->paginate(10);
        return response()->json([
            'results'      => $inventario->items(),
            'count'        => $inventario->total(),
            'current_page' => $inventario->currentPage(),
            'last_page'    => $inventario->lastPage(),
        ]);
    }

    public function search(Request $request)
    {
        $search = trim($request->get('query', ''));

        if (mb_strlen($search) > 0 && mb_strlen($search) < 3) {
            return response()->json([
                'results'      => [],
                'count'        => 0,
                'current_page' => 1,
                'last_page'    => 1,
            ]);
        }

        $query = Inventario::with('modelo')
            ->join('modelos', 'inventario.modelo_id', '=', 'modelos.id')
            ->select('inventario.*');

        if ($request->has('sucursal_id')) {
            $ids = $request->sucursal_id;
            if (is_string($ids)) {
                $ids = array_filter(array_map('trim', explode(',', $ids)));
            }
            $query->whereIn('inventario.sucursal_id', (array) $ids);
        }

        if ($search !== '') {
            $query->where(function ($sub) use ($search) {
                $sub->where('inventario.serie', 'like', "%{$search}%")
                    ->orWhere('inventario.color', 'like', "%{$search}%")
                    ->orWhere('inventario.motor', 'like', "%{$search}%")
                    ->orWhere('inventario.vin', 'like', "%{$search}%")
                    ->orWhere('inventario.estado', 'like', "%{$search}%")
                    ->orWhere('modelos.nombre', 'like', "%{$search}%")
                    ->orWhere('modelos.marca', 'like', "%{$search}%");
            });
        }

        $inventario = $query->paginate(10);
        return response()->json([
            'results'      => $inventario->items(),
            'count'        => $inventario->total(),
            'current_page' => $inventario->currentPage(),
            'last_page'    => $inventario->lastPage(),
        ]);
    }

    public function save(Request $request)
    {
        if ($request->filled('modelo_id')) {
            \App\Models\Modelo::findOrFail($request->modelo_id);
        }

        $validated = $request->validate([
            'modelo_id' => 'required',
            'sucursal_id' => 'required|exists:sucursales,id',
            'color' => 'required|string|max:60',
            'serie' => 'required|string|max:60|unique:inventario,serie',
            'motor' => 'required|string|max:60',
            'vin' => 'required|string|size:17|unique:inventario,vin',
            'estado' => 'in:disponible,vendida,reservada,defectuosa',
        ]);

        $item = Inventario::create($validated);
        return response()->json($item, 201);
    }
}
