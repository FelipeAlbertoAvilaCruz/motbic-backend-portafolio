<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function index()
    {
        return response()->json(Sucursal::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $sucursal = Sucursal::create($validated);
        return response()->json($sucursal, 200);
    }

    public function show($id)
    {
        return response()->json(Sucursal::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $sucursal = Sucursal::findOrFail($id);
        $sucursal->update($request->all());
        return response()->json($sucursal);
    }

    public function destroy($id)
    {
        Sucursal::destroy($id);
        return response()->json(null, 204);
    }

    public function getUsers($id)
    {
        $sucursal = Sucursal::findOrFail($id);
        return response()->json($sucursal->users);
    }
}
