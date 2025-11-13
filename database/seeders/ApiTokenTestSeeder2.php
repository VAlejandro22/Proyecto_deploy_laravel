<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Producto;
use Illuminate\Support\Facades\Hash;

class ApiTokenTestSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Crear un cliente de prueba
        $cliente = Cliente::firstOrCreate(
            ['email' => 'cliente.api@test.com'],
            [
                'nombre' => 'Cliente API Test',
                'telefono' => '123456789',
                'is_active' => true
            ]
        );

        // Crear un usuario para el cliente
        $user = User::firstOrCreate(
            ['email' => 'cliente.api@test.com'],
            [
                'name' => 'Cliente API Test',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );

        // Vincular cliente con usuario
        $cliente->update(['user_id' => $user->id]);

        // Asignar rol de cliente si existe
        try {
            if (!$user->hasRole('Cliente')) {
                $user->assignRole('Cliente');
            }
        } catch (\Exception $e) {
            // Rol no existe, continuar
        }

        // Crear algunos productos de prueba si no existen
        $producto1 = Producto::firstOrCreate(
            ['nombre' => 'Producto API Test 1'],
            [
                'precio' => 100.00,
                'stock' => 50,
                'is_active' => true
            ]
        );

        $producto2 = Producto::firstOrCreate(
            ['nombre' => 'Producto API Test 2'],
            [
                'precio' => 200.00,
                'stock' => 30,
                'is_active' => true
            ]
        );

        // Crear un usuario administrador para crear facturas
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin Test',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now()
            ]
        );

        try {
            if (!$admin->hasRole('Administrador')) {
                $admin->assignRole('Administrador');
            }
        } catch (\Exception $e) {
            // Rol no existe, continuar
        }

        // Crear algunas facturas de prueba para el cliente
        for ($i = 1; $i <= 3; $i++) {
            $factura = Factura::firstOrCreate([
                'cliente_id' => $cliente->id,
                'user_id' => $admin->id,
                'total' => 100 * $i,
                'estado' => $i === 3 ? 'anulada' : 'activa'
            ]);

            // Asociar productos a la factura
            if (!$factura->productos()->exists()) {
                $factura->productos()->attach($producto1->id, [
                    'cantidad' => $i,
                    'precio_unitario' => $producto1->precio
                ]);
            }
        }

        $this->command->info('Datos de prueba para API tokens creados exitosamente');
        $this->command->info("Cliente: {$cliente->email}");
        $this->command->info("Usuario: {$user->email}");
        $this->command->info("Facturas creadas: 3");
    }
}
