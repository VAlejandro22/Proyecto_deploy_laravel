<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Cliente;
use App\Models\Factura;

echo "=== Diagnóstico de relaciones ===" . PHP_EOL;

$user = User::find(19);
echo "Usuario ID 19: " . $user->name . " (" . $user->email . ")" . PHP_EOL;

$cliente = $user->cliente;
if ($cliente) {
    echo "Cliente asociado: ID " . $cliente->id . " - " . $cliente->nombre . PHP_EOL;
} else {
    echo "❌ Usuario no tiene cliente asociado" . PHP_EOL;
}

echo PHP_EOL . "=== Clientes con user_id 19 ===" . PHP_EOL;
$clientes = Cliente::where('user_id', 19)->get();
foreach ($clientes as $c) {
    echo "Cliente ID " . $c->id . ": " . $c->nombre . " (" . $c->email . ")" . PHP_EOL;
}

echo PHP_EOL . "=== Factura 22 ===" . PHP_EOL;
$factura = Factura::find(22);
echo "Factura ID 22 - Cliente ID: " . $factura->cliente_id . PHP_EOL;
echo "Cliente de la factura: " . $factura->cliente->nombre . PHP_EOL;
echo "User ID del cliente: " . ($factura->cliente->user_id ?? 'NULL') . PHP_EOL;

echo PHP_EOL . "=== Solución ===" . PHP_EOL;
if ($factura->cliente->user_id != 19) {
    echo "Actualizando cliente ID " . $factura->cliente_id . " para asociarlo al usuario 19..." . PHP_EOL;
    $factura->cliente->update(['user_id' => 19]);
    echo "✅ Cliente actualizado" . PHP_EOL;
} else {
    echo "✅ La asociación ya es correcta" . PHP_EOL;
}
