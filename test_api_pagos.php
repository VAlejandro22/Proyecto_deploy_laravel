<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Factura;

echo "=== Test de API de Pagos ===" . PHP_EOL;

// Obtener el usuario Cliente correcto (user_id 19)
$user = User::find(19);
$token = "20|FY6FYfpXLscAwWmKkCkKtrg8qiv3Flv1tabcTEZMe42979de";

echo "Usuario: " . $user->name . " (" . $user->email . ")" . PHP_EOL;
echo "Roles: " . $user->roles->pluck('name')->implode(', ') . PHP_EOL;

// Verificar si la factura 22 existe y corresponde a este cliente
$factura = Factura::find(22);
if ($factura) {
    echo "Factura encontrada:" . PHP_EOL;
    echo "  ID: " . $factura->id . PHP_EOL;
    echo "  Cliente ID: " . $factura->cliente_id . PHP_EOL;
    echo "  Cliente User ID: " . ($factura->cliente->user_id ?? 'null') . PHP_EOL;
    echo "  Usuario actual ID: " . $user->id . PHP_EOL;
    echo "  ¿Corresponde al usuario?: " . ($factura->cliente->user_id == $user->id ? 'SI' : 'NO') . PHP_EOL;
    echo "  Total: $" . $factura->total . PHP_EOL;
    echo "  Estado: " . $factura->estado . PHP_EOL;
}

// Test usando curl directo
$url = 'http://localhost:8000/api/cliente/pagos';
$data = [
    'factura_id' => 22,
    'monto' => 74.75,
    'monto_pagado' => 74.75,
    'numero_transaccion' => 'TRF-12345678',
    'tipo_pago' => 'transferencia',
    'referencia' => 'TRF-12345678',
    'descripcion' => 'Pago completo de factura FAC-000022',
    'observaciones' => 'Pago de prueba desde API'
];

$headers = [
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
    'Content-Type: application/json'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo PHP_EOL . "=== Respuesta de la API ===" . PHP_EOL;
echo "Código HTTP: " . $httpCode . PHP_EOL;
echo "Respuesta: " . $response . PHP_EOL;
