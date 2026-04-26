<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Listar todos los roles.
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json(['results' => $roles]);
    }

    /**
     * Crear un nuevo rol.
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'slug' => 'required|string|max:255|unique:roles,slug',
            'description' => 'nullable|string|max:255',
        ]);

        $role = Role::create($validated);

        return response()->json([
            'message' => 'Rol creado exitosamente',
            'role' => $role
        ], 200);
    }

    /**
     * Mostrar un rol específico.
     */
    public function get($id)
    {
        $role = Role::findOrFail($id);
        return response()->json(['results' => $role]);
    }

    /**
     * Actualizar un rol.
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'slug' => ['sometimes', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description' => 'nullable|string|max:255',
        ]);

        $role->update($validated);

        return response()->json([
            'message' => 'Rol actualizado exitosamente',
            'role' => $role
        ]);
    }

    /**
     * Eliminar un rol.
     */
    public function delete($id)
    {
        $role = Role::findOrFail($id);
        
        // Verificar si hay usuarios asignados antes de borrar
        if ($role->users()->exists()) {
             return response()->json(['error' => 'No se puede eliminar el rol porque tiene usuarios asignados.'], 409);
        }

        $role->delete();

        return response()->json([
            'message' => 'Rol eliminado exitosamente'
        ]);
    }
}
