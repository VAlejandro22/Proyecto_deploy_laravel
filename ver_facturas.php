<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Factura;

echo "=== Facturas disponibles para pagos ===" . PHP_EOL;

$facturas = Factura::with('cliente')->get();

foreach ($facturas as $factura) {
    echo "ID: " . $factura->id . PHP_EOL;
    echo "Cliente: " . ($factura->cliente->nombre ?? 'Sin cliente') . PHP_EOL;
    echo "Total: $" . number_format($factura->total, 2) . PHP_EOL;
    echo "Estado: " . $factura->estado . PHP_EOL;
    echo "Fecha: " . $factura->fecha_emision . PHP_EOL;
    echo "---" . PHP_EOL;
}
