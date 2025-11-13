<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos
        $permissions = [
            // Permisos de clientes
            'view_clientes',
            'create_clientes',
            'edit_clientes',
            'delete_clientes',
            'manage_roles', // Solo para administradores
            
            // Permisos de productos
            'view_productos',
            'create_productos',
            'edit_productos',
            'delete_productos',
            
            // Permisos de facturación
            'view_facturas',
            'create_facturas',
            'edit_facturas',
            'delete_facturas',
            'anular_facturas',
            
            // Permisos de reportes
            'view_reportes',
            
            // Permisos de auditoría
            'view_auditoria',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $secretarioRole = Role::firstOrCreate(['name' => 'Secretario']);
        $bodegaRole = Role::firstOrCreate(['name' => 'Bodega']);
        $ventasRole = Role::firstOrCreate(['name' => 'Ventas']);

        // Asignar permisos a roles

        // Administrador: todos los permisos
        $adminRole->givePermissionTo(Permission::all());

        // Secretario: solo ver clientes y facturas
        $secretarioRole->givePermissionTo([
            'view_clientes',
            'view_facturas',
            'view_reportes'
        ]);

        // Bodega: solo gestionar productos
        $bodegaRole->givePermissionTo([
            'view_productos',
            'create_productos',
            'edit_productos',
            'delete_productos'
        ]);

        // Ventas: solo gestionar facturas
        $ventasRole->givePermissionTo([
            'view_clientes',
            'view_productos',
            'view_facturas',
            'create_facturas',
            'edit_facturas',
            'anular_facturas'
        ]);

        // Crear usuario administrador por defecto
        $admin = User::firstOrCreate(
            ['email' => 'admin@facturacion.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('admin123'),
                'is_active' => true
            ]
        );

        $admin->assignRole('Administrador');

        $this->command->info('Roles y permisos creados exitosamente');
        $this->command->info('Usuario administrador creado: admin@facturacion.com / admin123');
    }
}
