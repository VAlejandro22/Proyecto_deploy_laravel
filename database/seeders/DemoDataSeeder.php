<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Factura;
use App\Models\User;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // Crear algunos clientes de ejemplo
        $clientes = [
            ['nombre' => 'Juan Pérez', 'email' => 'juan@ejemplo.com', 'telefono' => '123456789', 'is_active' => true],
            ['nombre' => 'María García', 'email' => 'maria@ejemplo.com', 'telefono' => '987654321', 'is_active' => true],
            ['nombre' => 'Carlos López', 'email' => 'carlos@ejemplo.com', 'telefono' => '456789123', 'is_active' => true],
            ['nombre' => 'Ana Martínez', 'email' => 'ana@ejemplo.com', 'telefono' => '789123456', 'is_active' => true],
            ['nombre' => 'Luis Rodríguez', 'email' => 'luis@ejemplo.com', 'telefono' => '321654987', 'is_active' => false],
        ];

        foreach ($clientes as $clienteData) {
            Cliente::firstOrCreate(['email' => $clienteData['email']], $clienteData);
        }

        // Crear algunos productos de ejemplo
        $productos = [
            ['nombre' => 'Laptop Dell XPS 13', 'descripcion' => 'Laptop ultrabook con procesador Intel i7', 'stock' => 5, 'precio' => 1200.00, 'is_active' => true],
            ['nombre' => 'Mouse Logitech MX Master 3', 'descripcion' => 'Mouse inalámbrico ergonómico', 'stock' => 25, 'precio' => 85.00, 'is_active' => true],
            ['nombre' => 'Teclado Mecánico RGB', 'descripcion' => 'Teclado gaming con switches Cherry MX', 'stock' => 15, 'precio' => 120.00, 'is_active' => true],
            ['nombre' => 'Monitor 4K Samsung', 'descripcion' => 'Monitor 27 pulgadas 4K UHD', 'stock' => 8, 'precio' => 400.00, 'is_active' => true],
            ['nombre' => 'Webcam HD', 'descripcion' => 'Cámara web 1080p para videoconferencias', 'stock' => 3, 'precio' => 65.00, 'is_active' => true],
            ['nombre' => 'Auriculares Bluetooth', 'descripcion' => 'Auriculares inalámbricos con cancelación de ruido', 'stock' => 2, 'precio' => 180.00, 'is_active' => true],
        ];

        foreach ($productos as $productoData) {
            Producto::firstOrCreate(['nombre' => $productoData['nombre']], $productoData);
        }

        // Crear algunas facturas de ejemplo
        $admin = User::where('email', 'admin@facturacion.com')->first();
        $ventas = User::where('email', 'ventas@facturacion.com')->first();
        
        if ($admin && $ventas) {
            $clientes = Cliente::limit(3)->get();
            $productos = Producto::limit(6)->get();

            if ($clientes->count() >= 3 && $productos->count() >= 6) {
                // Factura 1
                $factura1 = Factura::firstOrCreate([
                    'user_id' => $admin->id,
                    'cliente_id' => $clientes[0]->id,
                    'estado' => 'activa',
                    'total' => 1485.00
                ]);

                if ($factura1->productos()->count() == 0) {
                    $factura1->productos()->attach([
                        $productos[0]->id => ['cantidad' => 1, 'precio_unitario' => 1200.00], // Laptop
                        $productos[1]->id => ['cantidad' => 2, 'precio_unitario' => 85.00],   // Mouse
                        $productos[2]->id => ['cantidad' => 1, 'precio_unitario' => 120.00]   // Teclado
                    ]);
                }

                // Factura 2
                $factura2 = Factura::firstOrCreate([
                    'user_id' => $ventas->id,
                    'cliente_id' => $clientes[1]->id,
                    'estado' => 'activa',
                    'total' => 465.00
                ]);

                if ($factura2->productos()->count() == 0) {
                    $factura2->productos()->attach([
                        $productos[3]->id => ['cantidad' => 1, 'precio_unitario' => 400.00], // Monitor
                        $productos[4]->id => ['cantidad' => 1, 'precio_unitario' => 65.00]   // Webcam
                    ]);
                }

                // Factura 3
                $factura3 = Factura::firstOrCreate([
                    'user_id' => $ventas->id,
                    'cliente_id' => $clientes[2]->id,
                    'estado' => 'activa',
                    'total' => 180.00
                ]);

                if ($factura3->productos()->count() == 0) {
                    $factura3->productos()->attach([
                        $productos[5]->id => ['cantidad' => 1, 'precio_unitario' => 180.00]  // Auriculares
                    ]);
                }
            }
        }

        $this->command->info('Datos de demostración creados exitosamente:');
        $this->command->info('- ' . Cliente::count() . ' clientes');
        $this->command->info('- ' . Producto::count() . ' productos');
        $this->command->info('- ' . Factura::count() . ' facturas');
    }
}
