<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Factura;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\RolesTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles first
        $this->call(RolesTableSeeder::class);

        // Create admin user (only if it doesn't exist)
        $admin = User::firstOrCreate(
            ['email' => 'admin@empresa.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
                'is_active' => true,
            ]
        );

        // Also create alternative admin if needed
        $admin2 = User::firstOrCreate(
            ['email' => 'admin@facturacion.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
                'is_active' => true,
            ]
        );

        // Create test user (only if it doesn't exist)
        $user = User::firstOrCreate(
            ['email' => 'demo@facturacion.com'],
            [
                'name' => 'Usuario Demo',
                'password' => Hash::make('demo123'),
                'is_active' => true,
            ]
        );

        // Assign roles to users
        $adminRole = Role::where('name', 'Administrador')->first();
        $ventasRole = Role::where('name', 'Ventas')->first();

        if ($adminRole) {
            if (!$admin->hasRole('Administrador')) {
                $admin->assignRole($adminRole);
            }
            if (!$admin2->hasRole('Administrador')) {
                $admin2->assignRole($adminRole);
            }
        }

        if ($ventasRole && !$user->hasRole('Ventas')) {
            $user->assignRole($ventasRole);
        }

        // Create sample clients (only if none exist)
        if (Cliente::count() == 0) {
            $clientes = [
                [
                    'nombre' => 'Juan Pérez García',
                    'email' => 'juan.perez@email.com',
                    'telefono' => '+57 300 123 4567',
                ],
                [
                    'nombre' => 'María Rodríguez López',
                    'email' => 'maria.rodriguez@empresa.com',
                    'telefono' => '+57 311 987 6543',
                ],
                [
                    'nombre' => 'Carlos Hernández Silva',
                    'email' => 'carlos.hernandez@negocio.co',
                    'telefono' => '+57 312 456 7890',
                ],
                [
                    'nombre' => 'Ana Martínez Torres',
                    'email' => 'ana.martinez@tienda.com',
                    'telefono' => '+57 313 654 3210',
                ],
                [
                    'nombre' => 'Luis González Ramírez',
                    'email' => 'luis.gonzalez@comercio.co',
                    'telefono' => '+57 314 789 0123',
                ],
            ];

            foreach ($clientes as $clienteData) {
                Cliente::create($clienteData);
            }
        }

        // Create sample products (only if none exist)
        if (Producto::count() == 0) {
            $productos = [
                [
                    'nombre' => 'Laptop Dell Inspiron 15',
                    'descripcion' => 'Laptop Dell Inspiron 15, Intel Core i5, 8GB RAM, 256GB SSD, Windows 11',
                    'precio' => 2850000.00,
                    'stock' => 15,
                ],
                [
                    'nombre' => 'iPhone 14 128GB',
                    'descripcion' => 'Apple iPhone 14 de 128GB en color azul, pantalla de 6.1 pulgadas',
                    'precio' => 3200000.00,
                    'stock' => 8,
                ],
                [
                    'nombre' => 'Samsung Galaxy S23',
                    'descripcion' => 'Samsung Galaxy S23 256GB, cámara de 50MP, pantalla Dynamic AMOLED',
                    'precio' => 2950000.00,
                    'stock' => 12,
                ],
                [
                    'nombre' => 'Audífonos Sony WH-1000XM4',
                    'descripcion' => 'Audífonos inalámbricos con cancelación de ruido, batería de 30 horas',
                    'precio' => 890000.00,
                    'stock' => 25,
                ],
                [
                    'nombre' => 'Teclado Mecánico Logitech',
                    'descripcion' => 'Teclado mecánico gaming RGB, switches azules, retroiluminado',
                    'precio' => 320000.00,
                    'stock' => 30,
                ],
                [
                    'nombre' => 'Monitor LG 27" 4K',
                    'descripcion' => 'Monitor LG UltraFine 27 pulgadas, resolución 4K, IPS, USB-C',
                    'precio' => 1450000.00,
                    'stock' => 10,
                ],
                [
                    'nombre' => 'Mouse Gaming Razer',
                    'descripcion' => 'Mouse gaming Razer DeathAdder V3, sensor óptico 30K DPI',
                    'precio' => 180000.00,
                    'stock' => 45,
                ],
                [
                    'nombre' => 'Tablet iPad Air 10.9"',
                    'descripcion' => 'Apple iPad Air 10.9 pulgadas, chip M1, 64GB, Wi-Fi',
                    'precio' => 2100000.00,
                    'stock' => 6,
                ],
                [
                    'nombre' => 'Impresora HP LaserJet',
                    'descripcion' => 'Impresora HP LaserJet Pro M404n, monocromática, red ethernet',
                    'precio' => 680000.00,
                    'stock' => 8,
                ],
                [
                    'nombre' => 'Disco Duro Externo 2TB',
                    'descripcion' => 'Disco duro externo Seagate 2TB, USB 3.0, portátil',
                    'precio' => 280000.00,
                    'stock' => 20,
                ],
                [
                    'nombre' => 'Cámara Web Logitech C920',
                    'descripcion' => 'Cámara web Full HD 1080p, micrófono estéreo integrado',
                    'precio' => 250000.00,
                    'stock' => 35,
                ],
                [
                    'nombre' => 'Router WiFi 6 TP-Link',
                    'descripcion' => 'Router inalámbrico AX1800, WiFi 6, 4 antenas, dual band',
                    'precio' => 380000.00,
                    'stock' => 18,
                ],
            ];

            foreach ($productos as $productoData) {
                Producto::create($productoData);
            }
        }

        // Create sample invoices (only if none exist)
        if (Factura::count() == 0) {
            $this->createSampleInvoices();
        }

        $this->command->info('✅ Base de datos poblada con datos de ejemplo');
        $this->command->info('📧 Admin: admin@facturacion.com | Contraseña: admin123');
        $this->command->info('👤 Demo: demo@facturacion.com | Contraseña: demo123');
    }

    private function createSampleInvoices()
    {
        $clientes = Cliente::all();
        $productos = Producto::all();
        $users = User::all();

        // Create 8 sample invoices
        for ($i = 1; $i <= 8; $i++) {
            $cliente = $clientes->random();
            $user = $users->random(); // Random user who creates the invoice
            
            $factura = Factura::create([
                'user_id' => $user->id,
                'cliente_id' => $cliente->id,
                'total' => 0, // Will be calculated
                'estado' => $i <= 6 ? 'activa' : 'anulada',
            ]);

            // Add 1-4 random products to each invoice
            $numProductos = rand(1, 4);
            $selectedProducts = $productos->random($numProductos);
            $total = 0;

            foreach ($selectedProducts as $producto) {
                $cantidad = rand(1, min(3, $producto->stock));
                $precio = $producto->precio;
                $subtotal = $cantidad * $precio;
                
                $factura->productos()->attach($producto->id, [
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                ]);
                
                $total += $subtotal;
            }

            // Calculate total with IVA (19%)
            $totalConIva = $total * 1.19;
            $factura->update(['total' => $totalConIva]);
        }
    }
}
