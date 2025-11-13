<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pago;

echo "=== Verificación de Pagos en la Base de Datos ===" . PHP_EOL;

$pagos = Pago::with(['factura.cliente', 'user'])->get();

echo "Total de pagos encontrados: " . $pagos->count() . PHP_EOL . PHP_EOL;

foreach ($pagos as $pago) {
    echo "Pago ID: " . $pago->id . PHP_EOL;
    echo "Factura: " . ($pago->factura->numero_factura ?? 'N/A') . PHP_EOL;
    echo "Cliente: " . ($pago->factura->cliente->nombre ?? 'N/A') . PHP_EOL;
    echo "Usuario: " . ($pago->user->name ?? 'N/A') . PHP_EOL;
    echo "Monto: $" . number_format($pago->monto_pagado, 2) . PHP_EOL;
    echo "Estado: " . $pago->estado . PHP_EOL;
    echo "Tipo Pago: " . $pago->tipo_pago . PHP_EOL;
    echo "Número Transacción: " . $pago->numero_transaccion . PHP_EOL;
    echo "Fecha: " . $pago->created_at . PHP_EOL;
    echo "---" . PHP_EOL;
}
