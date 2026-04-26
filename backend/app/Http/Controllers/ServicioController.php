<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ServicioController extends Controller
{

    public function counter(Request $request)
    {
        $query = Servicio::query()->whereNot('moto_externa', true);

        if ($request->filled('sucursal_id')) {
            $sucursalId = $request->get('sucursal_id');
            $query->whereHas('inventario', function ($q) use ($sucursalId) {
                $q->where('sucursal_id', $sucursalId);
            });
        }

        $total       = (clone $query)->count();
        $programados = (clone $query)->where('estado', 'programado')->count();
        $completados = (clone $query)->where('estado', 'completado')->count();
        $vencidos    = (clone $query)->where('estado', 'vencido')->count();

        return response()->json([
            'total'       => $total,
            'programados' => $programados,
            'completados' => $completados,
            'vencidos'    => $vencidos,
        ]);
    }

    public function search(Request $request)
    {
        $page = $request->get('page', 1);

        $queryBuilder = Servicio::with([
            'cliente',
            'inventario.modelo',
            'venta'
        ]);

        $queryBuilder->whereNot('moto_externa', true);

        if ($request->filled('sucursal_id')) {
            $queryBuilder->whereHas('inventario', function ($q) use ($request) {
                $q->where('sucursal_id', $request->get('sucursal_id'));
            });
        }

        $estadoMap = [
            'pendientes'   => 'programado',
            'completados'  => 'completado',
            'vencidos'     => 'vencido',
        ];

        $estado = strtolower($request->get('estado', 'pendientes'));

        if (isset($estadoMap[$estado])) {
            $queryBuilder->where('estado', $estadoMap[$estado]);
        }

        $fechaInicio = $request->get(
            'fecha_inicio',
            now()->toDateString()
        );

        $fechaFin = $request->get(
            'fecha_fin',
            now()->addDays(7)->toDateString()
        );

        $queryBuilder->whereBetween('fecha_programada', [
            $fechaInicio,
            $fechaFin
        ]);

        if ($request->filled('query')) {
            $search = $request->query('query');

            $queryBuilder->where(function ($q) use ($search) {

                $q->where('tipo_servicio', 'LIKE', "%{$search}%")

                ->orWhereHas('cliente', function ($cliente) use ($search) {
                    $cliente->where('nombres', 'LIKE', "%{$search}%")
                            ->orWhere('apellidos', 'LIKE', "%{$search}%");
                })

                ->orWhereHas('inventario.modelo', function ($modelo) use ($search) {
                    $modelo->where('marca', 'LIKE', "%{$search}%")
                        ->orWhere('nombre', 'LIKE', "%{$search}%");
                });
            });
        }

        $servicios = $queryBuilder->paginate(100, ['*'], 'page', $page);

        $results = $servicios->getCollection()->map(function ($servicio) {
            return [
                'id' => $servicio->id,
                'cliente' => $servicio->cliente ? [
                    'id' => $servicio->cliente->id,
                    'nombres' => $servicio->cliente->nombres,
                    'apellidos' => $servicio->cliente->apellidos,
                    'telefono' => $servicio->cliente->telefono,
                ] : null,
                'motocicleta' => $servicio->inventario ? [
                    'id' => $servicio->inventario->id,
                    'serie' => $servicio->inventario->serie,
                    'modelo' => $servicio->inventario->modelo ? [
                        'marca' => $servicio->inventario->modelo->marca,
                        'nombre' => $servicio->inventario->modelo->nombre,
                        'anio' => $servicio->inventario->modelo->anio,
                    ] : null,
                ] : null,
                'tipo_servicio' => $servicio->tipo_servicio,
                'fecha_venta' => optional($servicio->venta?->fecha)->format('Y-m-d'),
                'fecha_programada' => optional($servicio->fecha_programada)->format('Y-m-d'),
                'dias_restantes' => $servicio->fecha_programada
                    ? now()->diffInDays($servicio->fecha_programada, false)
                    : null,
                'estado' => $servicio->estado,
                'sucursal_id' => $servicio->inventario?->sucursal_id,
                'moto_externa' => $servicio->moto_externa,
            ];
        });

        return response()->json([
            'results' => $results,
            'count' => $servicios->total(),
            'current_page' => $servicios->currentPage(),
            'last_page' => $servicios->lastPage(),
        ]);
    }

    public function get($id)
    {
        $servicios = Servicio::findOrFail($id);
        return response()->json(['results' => $servicios]);
    }

    public function get_by_motocicleta(Request $request, $motocicleta_id)
    {
        $page = $request->get('page', 1);

        $servicios = Servicio::where('inventario_id', $motocicleta_id)
            ->paginate(100, ['*'], 'page', $page);

        return response()->json([
            'results' => $servicios->items(),
            'count' => count($servicios->items()),
            'current_page' => $servicios->currentPage(),
            'total_pages' => $servicios->lastPage()
        ]);
    }

    public function get_by_cliente_id(Request $request, $clienteId)
    {
        $page = $request->get('page', 1);

        $servicios = Servicio::where('cliente_id', $clienteId)
            ->paginate(100, ['*'], 'page', $page);

        return response()->json([
            'results' => $servicios->items(),
            'count' => $servicios->count(),
            'current_page' => $servicios->currentPage(),
            'total_pages' => $servicios->lastPage(),
        ]);
    }

    public function get_by_moto_externa(Request $request)
    {
        $page = $request->get('page', 1);

        $queryBuilder = Servicio::with(['cliente'])
            ->where('moto_externa', true);

        $estadoMap = [
            'pendientes'   => 'programado',
            'completados'  => 'completado',
            'vencidos'     => 'vencido',
        ];

        $estado = strtolower($request->get('estado', 'pendientes'));

        if (isset($estadoMap[$estado])) {
            $queryBuilder->where('estado', $estadoMap[$estado]);
        }

        $fechaInicio = $request->get(
            'fecha_inicio',
            now()->toDateString()
        );

        $fechaFin = $request->get(
            'fecha_fin',
            now()->addDays(7)->toDateString()
        );

        $queryBuilder->whereBetween('fecha_programada', [
            $fechaInicio,
            $fechaFin
        ]);

        $servicios = $queryBuilder->paginate(50, ['*'], 'page', $page);

        $results = $servicios->getCollection()->map(function ($servicio) {
            return [
                'id' => $servicio->id,
                'cliente' => $servicio->cliente ? [
                    'id' => $servicio->cliente->id,
                    'nombres' => $servicio->cliente->nombres,
                    'apellidos' => $servicio->cliente->apellidos,
                    'telefono' => $servicio->cliente->telefono,
                ] : null,
                'tipo_servicio' => $servicio->tipo_servicio,
                'fecha_programada' => optional($servicio->fecha_programada)->format('Y-m-d'),
                'dias_restantes' => $servicio->fecha_programada
                    ? now()->diffInDays($servicio->fecha_programada, false)
                    : null,
                'estado' => $servicio->estado,
                'detalles_moto_externa' => $servicio->detalles_moto_externa,
                'notas' => $servicio->notas,
                'costo' => $servicio->costo,
                'moto_externa' => $servicio->moto_externa,
            ];
        });

        return response()->json([
            'results' => $results,
            'count' => $servicios->total(),
            'current_page' => $servicios->currentPage(),
            'last_page' => $servicios->lastPage(),
        ]);
    }

    public function save(Request $request)
    {
        $motoExterna = $request->get('moto_externa', false);

        if ($motoExterna === true || $motoExterna === 'true') {
            $validated = $request->validate([
                'cliente_id' => 'required|exists:clientes,id',
                'tipo_servicio' => 'required|string|max:100',
                'fecha_programada' => 'required|date',
                'moto_externa' => 'required|boolean',
                'detalles_moto_externa' => 'nullable|string',
                'notas' => 'nullable|string',
                'costo' => 'nullable|numeric',
            ]);

            $datosGuardar = [
                'moto_externa' => true,
                'detalles_moto_externa' => $request->get('detalles_moto_externa', ''),
                'cliente_id' => $validated['cliente_id'],
                'tipo_servicio' => $validated['tipo_servicio'],
                'fecha_programada' => $validated['fecha_programada'],
                'notas' => $validated['notas'],
                'costo' => $validated['costo'],
                'estado' => 'programado',
            ];
        } else {
            $validated = $request->validate([
                'cliente_id' => 'required|exists:clientes,id',
                'inventario_id' => 'required|exists:inventario,id',
                'venta_id' => 'nullable|exists:ventas,id',
                'tipo_servicio' => 'required|string|max:100',
                'fecha_programada' => 'required|date',
                'notas' => 'nullable|string',
                'costo' => 'nullable|numeric',
            ]);

            $datosGuardar = array_merge($validated, [
                'moto_externa' => false,
                'detalles_moto_externa' => '',
                'estado' => 'programado',
            ]);
        }

        $servicio = Servicio::create($datosGuardar);
        return response()->json($servicio, 201);
    }

    public function complete(Request $request, $id)
    {
        $servicio = Servicio::findOrFail($id);
        $validated = $request->validate([
            'fecha_realizada' => 'required|date',
            'notas' => 'nullable|string',
        ]);

        $servicio->update(array_merge($validated, ['estado' => 'completado']));
        return response()->json(['message' => 'Servicio marcado como completado exitosamente', 'data' => $servicio]);
    }

    public function delete($id)
    {
        $servicio = Servicio::findOrFail($id);

        if ($servicio->estado !== 'programado') {
            return response()->json(['message' => 'Solo se pueden eliminar servicios en estado programado'], 422);
        }

        $servicio->delete();
        return response()->json(['message' => 'Servicio eliminado exitosamente']);
    }
}
