<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pago;

echo "=== Test directo del controlador de pagos ===" . PHP_EOL;

// Simular el comportamiento del controlador
$query = Pago::with(['factura.cliente', 'user'])
    ->orderBy('created_at', 'desc');

echo "Consulta base - Total pagos: " . $query->count() . PHP_EOL;

// Aplicar filtro por defecto (pendientes)
$queryPendientes = clone $query;
$queryPendientes->where('estado', 'pendiente');

echo "Solo pendientes: " . $queryPendientes->count() . PHP_EOL;

// Obtener los datos como lo hace el controlador
$pagos = $queryPendientes->paginate(15);

echo "Pagos paginados: " . $pagos->count() . PHP_EOL;

foreach ($pagos as $pago) {
    echo "- Pago ID " . $pago->id . " - Estado: " . $pago->estado . " - Factura: " . $pago->factura->numero_factura . PHP_EOL;
}
