<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // Usuario Secretario
        $secretario = User::firstOrCreate(
            ['email' => 'secretario@facturacion.com'],
            [
                'name' => 'María Secretaria',
                'password' => bcrypt('secretario123'),
                'is_active' => true
            ]
        );
        $secretario->assignRole('Secretario');

        // Usuario Bodega
        $bodega = User::firstOrCreate(
            ['email' => 'bodega@facturacion.com'],
            [
                'name' => 'Juan Bodeguero',
                'password' => bcrypt('bodega123'),
                'is_active' => true
            ]
        );
        $bodega->assignRole('Bodega');

        // Usuario Ventas
        $ventas = User::firstOrCreate(
            ['email' => 'ventas@facturacion.com'],
            [
                'name' => 'Ana Vendedora',
                'password' => bcrypt('ventas123'),
                'is_active' => true
            ]
        );
        $ventas->assignRole('Ventas');

        // Usuario sin rol para probar dashboard default
        $sinrol = User::firstOrCreate(
            ['email' => 'usuario@facturacion.com'],
            [
                'name' => 'Usuario Sin Rol',
                'password' => bcrypt('usuario123'),
                'is_active' => true
            ]
        );

        $this->command->info('Usuarios de ejemplo creados:');
        $this->command->info('- Admin: admin@facturacion.com / admin123');
        $this->command->info('- Secretario: secretario@facturacion.com / secretario123');
        $this->command->info('- Bodega: bodega@facturacion.com / bodega123');
        $this->command->info('- Ventas: ventas@facturacion.com / ventas123');
        $this->command->info('- Sin rol: usuario@facturacion.com / usuario123');
    }
}
