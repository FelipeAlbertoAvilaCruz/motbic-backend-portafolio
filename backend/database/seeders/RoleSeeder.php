<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles
        $admin = Role::firstOrCreate(['slug' => 'admin'], [
            'name' => 'Administrador',
            'description' => 'Acceso total al sistema'
        ]);

        $gerente = Role::firstOrCreate(['slug' => 'gerente'], [
            'name' => 'Gerente',
            'description' => 'Gestión de inventario y ventas'
        ]);

        $empleado = Role::firstOrCreate(['slug' => 'empleado'], [
            'name' => 'Empleado',
            'description' => 'Acceso limitado a operaciones básicas'
        ]);

        // Asignar rol de admin al primer usuario si existe
        $user = User::first();
        if ($user) {
            $user->role_id = $admin->id;
            $user->save();
        }
    }
}
