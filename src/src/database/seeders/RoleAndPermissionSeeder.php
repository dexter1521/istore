<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permisos para Vendedor
        Permission::firstOrCreate(['name' => 'ver pedidos', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'mover pedidos', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'crear notas', 'guard_name' => 'web']);

        // Permisos para Editor
        Permission::firstOrCreate(['name' => 'crear productos', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'editar productos', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'eliminar productos', 'guard_name' => 'web']); // Soft delete
        Permission::firstOrCreate(['name' => 'crear categorias', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'editar categorias', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'eliminar categorias', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'importar excel', 'guard_name' => 'web']);

        // Permisos para Solo Lectura (y otros)
        Permission::firstOrCreate(['name' => 'ver productos', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'ver reportes', 'guard_name' => 'web']);

        // Crear Roles y asignar permisos

        // Vendedor
        $role = Role::firstOrCreate(['name' => 'vendedor']);
        $role->givePermissionTo(['ver pedidos', 'mover pedidos', 'crear notas']);

        // Editor
        $role = Role::firstOrCreate(['name' => 'editor']);
        $role->givePermissionTo([
            'ver productos',
            'crear productos',
            'editar productos',
            'eliminar productos',
            'crear categorias',
            'editar categorias',
            'eliminar categorias',
            'importar excel'
        ]);

        // Solo Lectura
        $role = Role::firstOrCreate(['name' => 'solo lectura']);
        $role->givePermissionTo(['ver pedidos', 'ver productos', 'ver reportes']);

        // Admin (Control total)
        $role = Role::firstOrCreate(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());
    }
}
