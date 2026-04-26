<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClienteController extends Controller
{
    public function get_all(Request $request)
    {
        $page = $request->get('page', 1);

        $clientes = Cliente::orderBy('ultima_compra', 'desc')
            ->paginate(10, ['*'], 'page', $page);

        return response()->json([
            'results' => $clientes->items(),
            'count' => count($clientes->items()),
            'current_page' => $clientes->currentPage(),
            'total_pages' => $clientes->lastPage()
        ]);
    }

    public function get($id)
    {
        $cliente = Cliente::findOrFail($id);
        return response()->json(['results' => $cliente]);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'nombres' => 'required|string|max:120',
            'apellidos' => 'required|string|max:150',
            'telefono' => 'required|string|max:20|unique:clientes,telefono',
            'email' => 'required|email|max:150|unique:clientes,email',
            'rfc' => 'required|string|size:13|unique:clientes,rfc',
            'calle' => 'required|string|max:150',
            'colonia' => 'required|string|max:120',
            'ciudad' => 'required|string|max:120',
            'estado' => 'required|string|max:120',
            'codigo_postal' => 'required|string|size:5',
        ]);

        $cliente = Cliente::create($validated);
        return response()->json($cliente, 201);
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);
        $validated = $request->validate([
            'nombres' => 'string|max:120',
            'apellidos' => 'string|max:150',
            'telefono' => 'string|max:20|unique:clientes,telefono,' . $id,
            'email' => 'email|max:150|unique:clientes,email,' . $id,
            'rfc' => 'string|size:13|unique:clientes,rfc,' . $id,
            'calle' => 'string|max:150',
            'colonia' => 'string|max:120',
            'ciudad' => 'string|max:120',
            'estado' => 'string|max:120',
            'codigo_postal' => 'string|size:5',
            'estado_servicios' => 'in:al-dia,pendiente,vencido',
        ]);

        $cliente->update($validated);
        return response()->json(['message' => 'Cliente actualizado exitosamente', 'data' => $cliente]);
    }

    public function delete($id)
    {
        $cliente = Cliente::findOrFail($id);
        try {
            $cliente->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'message' => 'No se puede eliminar el cliente porque tiene servicios asociados.'
                ], 409);
            }
            throw $e;
        }
        return response()->json(['message' => 'Cliente eliminado exitosamente']);
    }

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1|max:150',
        ]);

        $q = $request->query('query');

        $clientes = Cliente::where('nombres', 'like', '%' . $q . '%')
            ->orWhere('apellidos', 'like', '%' . $q . '%')
            ->orWhere('email', 'like', '%' . $q . '%')
            ->orWhere('telefono', 'like', '%' . $q . '%')
            ->paginate(10);

        return response()->json([
            'results'      => $clientes->items(),
            'count'        => $clientes->total(),
            'current_page' => $clientes->currentPage(),
            'last_page'    => $clientes->lastPage(),
        ]);
    }
}
