<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use Illuminate\Http\Request;

class ModeloController extends Controller
{
    public function get()
    {
        $modelos = Modelo::orderBy('created_at', 'desc')->paginate(10);
        return response()->json([
            'results' => $modelos->items(),
            'count' => $modelos->total(),
            'current_page' => $modelos->currentPage(),
            'last_page' => $modelos->lastPage(),
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $modelos = Modelo::where('nombre', 'like', "%{$query}%")
            ->orWhere('marca', 'like', "%{$query}%")
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return response()->json([
            'results' => $modelos->items(),
            'count' => $modelos->total(),
            'current_page' => $modelos->currentPage(),
            'last_page' => $modelos->lastPage(),
        ]);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:120',
            'marca' => 'required|string|max:120',
            'anio' => 'required|string|size:4',
            'tipo_motor' => 'required|in:electrica,gasolina,hibrida',
            'cilindrada' => 'required|string|max:50',
            'precio' => 'required|numeric',
            'colores' => 'required|array',
            'imagen' => 'nullable|url',
        ]);

        $modelo = Modelo::create($validated);
        return response()->json($modelo, 201);
    }

    public function update(Request $request, $id)
    {
        $modelo = Modelo::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'string|max:120',
            'marca' => 'string|max:120',
            'anio' => 'string|size:4',
            'tipo_motor' => 'in:electrica,gasolina,hibrida',
            'cilindrada' => 'string|max:50',
            'precio' => 'numeric',
            'colores' => 'array',
            'imagen' => 'nullable|url',
        ]);

        $modelo->update($validated);
        return response()->json(['message' => 'Modelo actualizado exitosamente', 'data' => $modelo]);
    }

    public function delete($id)
    {
        $modelo = Modelo::findOrFail($id);
        $modelo->delete();
        return response()->json(['message' => 'Modelo eliminado exitosamente']);
    }
}
