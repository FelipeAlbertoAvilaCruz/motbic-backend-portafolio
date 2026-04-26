<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Listar usuarios.
     */
    public function index()
    {
        $users = User::with(['role', 'sucursales'])->paginate(15);
        return response()->json([
            'results' => $users->items(),
            'count' => $users->total(),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
        ]);
    }

    /**
     * Crear nuevo usuario.
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'sucursales' => 'nullable|array',
            'sucursales.*' => 'exists:sucursales,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
        ]);

        if (isset($validated['sucursales'])) {
            $user->sucursales()->sync($validated['sucursales']);
        }

        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'user' => $user->load(['role', 'sucursales'])
        ], 201);
    }

    /**
     * Mostrar usuario específico.
     */
    public function get($id)
    {
        $user = User::with(['role', 'sucursales'])->findOrFail($id);
        return response()->json(['results' => $user]);
    }

    /**
     * Actualizar usuario.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'username' => ['sometimes', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|string|min:8',
            'role_id' => 'sometimes|exists:roles,id',
            'sucursales' => 'nullable|array',
            'sucursales.*' => 'exists:sucursales,id',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        if (isset($validated['sucursales'])) {
            $user->sucursales()->sync($validated['sucursales']);
        }

        return response()->json([
            'message' => 'Usuario actualizado exitosamente',
            'user' => $user->load(['role', 'sucursales'])
        ]);
    }

    /**
     * Eliminar usuario.
     */
    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado exitosamente'
        ]);
    }
}
