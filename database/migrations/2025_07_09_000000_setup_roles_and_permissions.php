<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Create permissions
        $permissions = [
            'gestionar-usuarios',
            'gestionar-clientes', 
            'ver-clientes',
            'gestionar-productos',
            'ver-productos',
            'gestionar-facturas',
            'ver-facturas',
            'gestionar-reportes',
            'ver-reportes',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create admin role with all permissions
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $adminRole->syncPermissions($permissions);

        // Create other roles
        $secretarioRole = Role::firstOrCreate(['name' => 'Secretario']);
        $secretarioRole->givePermissionTo([
            'gestionar-clientes', 
            'ver-clientes',
            'gestionar-facturas',
            'ver-facturas',
            'ver-productos',
            'ver-reportes'
        ]);

        $bodegaRole = Role::firstOrCreate(['name' => 'Bodega']);
        $bodegaRole->givePermissionTo([
            'gestionar-productos',
            'ver-productos',
            'ver-facturas'
        ]);

        $ventasRole = Role::firstOrCreate(['name' => 'Ventas']);
        $ventasRole->givePermissionTo([
            'ver-clientes',
            'gestionar-facturas',
            'ver-facturas',
            'ver-productos',
            'ver-reportes'
        ]);

        // Assign admin role to admin users
        $adminEmails = ['admin@empresa.com', 'admin@facturacion.com'];
        foreach ($adminEmails as $email) {
            $user = User::where('email', $email)->first();
            if ($user && !$user->hasRole('Administrador')) {
                $user->assignRole($adminRole);
                echo "Admin role assigned to {$user->email}\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove roles and permissions
        Role::where('name', 'Administrador')->delete();
        Role::where('name', 'Secretario')->delete();
        Role::where('name', 'Bodega')->delete();
        Role::where('name', 'Ventas')->delete();
        
        Permission::whereIn('name', [
            'gestionar-usuarios',
            'gestionar-clientes', 
            'ver-clientes',
            'gestionar-productos',
            'ver-productos',
            'gestionar-facturas',
            'ver-facturas',
            'gestionar-reportes',
            'ver-reportes',
        ])->delete();
    }
};
