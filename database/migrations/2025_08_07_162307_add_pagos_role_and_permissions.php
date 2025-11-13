<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
        
        // Create new permissions for payments
        $newPermissions = [
            'gestionar-pagos',
            'ver-pagos',
            'validar-pagos',
        ];

        foreach ($newPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Pagos role
        $pagosRole = Role::firstOrCreate(['name' => 'Pagos']);
        $pagosRole->givePermissionTo([
            'gestionar-pagos',
            'ver-pagos',
            'validar-pagos',
            'ver-facturas' // Necesita ver facturas para validar pagos
        ]);

        // Give admin role the new permissions
        $adminRole = Role::where('name', 'Administrador')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($newPermissions);
        }

        // Create Cliente role if it doesn't exist
        $clienteRole = Role::firstOrCreate(['name' => 'Cliente']);
        // Los clientes pueden ver sus propias facturas y crear pagos
        $clienteRole->givePermissionTo(['ver-facturas']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove Pagos role
        Role::where('name', 'Pagos')->delete();
        Role::where('name', 'Cliente')->delete();
        
        // Remove new permissions
        Permission::whereIn('name', [
            'gestionar-pagos',
            'ver-pagos',
            'validar-pagos',
        ])->delete();
    }
};
