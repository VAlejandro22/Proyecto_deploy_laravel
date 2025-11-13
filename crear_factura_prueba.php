<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;

echo "=== Creando factura para cliente con usuario ===" . PHP_EOL;

// Buscar cliente con usuario
$cliente = Cliente::whereNotNull('user_id')->first();

if (!$cliente) {
    echo "❌ No hay clientes con usuario asociado" . PHP_EOL;
    exit;
}

echo "Cliente encontrado: " . $cliente->nombre . " (" . $cliente->email . ")" . PHP_EOL;

// Buscar un usuario con rol Ventas o Administrador para crear la factura
$vendedor = User::role(['Administrador', 'Ventas'])->first();

if (!$vendedor) {
    echo "❌ No hay usuarios con rol Vendedor o Administrador" . PHP_EOL;
    exit;
}

echo "Vendedor encontrado: " . $vendedor->name . PHP_EOL;

// Buscar un producto
$producto = Producto::first();

if (!$producto) {
    echo "❌ No hay productos disponibles" . PHP_EOL;
    exit;
}

echo "Producto encontrado: " . $producto->nombre . " - $" . $producto->precio . PHP_EOL;

// Crear factura
$factura = Factura::create([
    'user_id' => $vendedor->id, // Quien crea la factura
    'cliente_id' => $cliente->id, // A quien se le factura
    'fecha_emision' => now(),
    'fecha_vencimiento' => now()->addDays(30),
    'subtotal' => $producto->precio,
    'impuestos' => $producto->precio * 0.15, // 15% impuestos
    'total' => $producto->precio * 1.15,
    'estado' => 'activa',
    'notas' => 'Factura de prueba para testing de pagos API'
]);

// Asociar producto a la factura
$factura->productos()->attach($producto->id, [
    'cantidad' => 1,
    'precio_unitario' => $producto->precio
]);

echo "✅ Factura creada:" . PHP_EOL;
echo "   ID: " . $factura->id . PHP_EOL;
echo "   Número: " . $factura->numero_factura . PHP_EOL;
echo "   Total: $" . number_format($factura->total, 2) . PHP_EOL;
echo "   Cliente: " . $cliente->nombre . " (ID Usuario: " . $cliente->user_id . ")" . PHP_EOL;
echo "   Creada por: " . $vendedor->name . PHP_EOL;
