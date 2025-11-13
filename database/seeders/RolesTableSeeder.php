<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'gestionar-usuarios',
            'create_clientes',
            'view_clientes', 
            'edit_clientes',
            'delete_clientes',
            'manage_roles',
            'create_productos',
            'view_productos',
            'edit_productos',
            'delete_productos',
            'create_facturas',
            'view_facturas',
            'edit_facturas',
            'anular_facturas',
            'delete_facturas',
            'gestionar-reportes',
            'ver-reportes',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $adminRole->givePermissionTo($permissions); // Admin has all permissions

        $secretarioRole = Role::firstOrCreate(['name' => 'Secretario']);
        $secretarioRole->givePermissionTo([
            'create_clientes',
            'view_clientes', 
            'edit_clientes',
            'create_facturas',
            'view_facturas',
            'edit_facturas',
            'view_productos',
            'ver-reportes'
        ]);

        $bodegaRole = Role::firstOrCreate(['name' => 'Bodega']);
        $bodegaRole->givePermissionTo([
            'create_productos',
            'view_productos',
            'edit_productos',
            'view_facturas'
        ]);

        $ventasRole = Role::firstOrCreate(['name' => 'Ventas']);
        $ventasRole->givePermissionTo([
            'view_clientes',
            'create_facturas',
            'view_facturas',
            'edit_facturas',
            'view_productos',
            'ver-reportes'
        ]);
    }
}
