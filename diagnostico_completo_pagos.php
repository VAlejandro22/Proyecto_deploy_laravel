<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pago;
use App\Models\User;

echo "=== Diagnóstico completo de pagos ===" . PHP_EOL;

// 1. Verificar pagos en la base de datos
echo "1. Pagos totales en BD: " . Pago::count() . PHP_EOL;

// 2. Verificar pagos con relaciones
$pagos = Pago::with(['factura.cliente', 'user'])->get();
echo "2. Pagos con relaciones cargadas: " . $pagos->count() . PHP_EOL;

foreach ($pagos as $pago) {
    echo "   - Pago ID: " . $pago->id . PHP_EOL;
    echo "     Estado: " . $pago->estado . PHP_EOL;
    echo "     Factura: " . ($pago->factura ? $pago->factura->numero_factura : 'NULL') . PHP_EOL;
    echo "     Cliente: " . ($pago->factura && $pago->factura->cliente ? $pago->factura->cliente->nombre : 'NULL') . PHP_EOL;
    echo "     Usuario: " . ($pago->user ? $pago->user->name : 'NULL') . PHP_EOL;
    echo "     Created: " . $pago->created_at . PHP_EOL;
    echo "     ---" . PHP_EOL;
}

// 3. Simular la consulta del controlador sin filtros
echo PHP_EOL . "3. Simulando consulta del controlador SIN filtros:" . PHP_EOL;
$queryTodos = Pago::with(['factura.cliente', 'user'])
    ->orderBy('created_at', 'desc')
    ->get();
echo "   Resultado sin filtros: " . $queryTodos->count() . " pagos" . PHP_EOL;

// 4. Simular la consulta del controlador CON filtro pendiente (por defecto)
echo PHP_EOL . "4. Simulando consulta del controlador CON filtro pendiente:" . PHP_EOL;
$queryPendientes = Pago::with(['factura.cliente', 'user'])
    ->where('estado', 'pendiente')
    ->orderBy('created_at', 'desc')
    ->get();
echo "   Resultado con filtro 'pendiente': " . $queryPendientes->count() . " pagos" . PHP_EOL;

// 5. Verificar si hay problemas con las relaciones
echo PHP_EOL . "5. Verificando integridad de relaciones:" . PHP_EOL;
foreach ($pagos as $pago) {
    $problemas = [];
    
    if (!$pago->factura) {
        $problemas[] = "Factura faltante (factura_id: " . $pago->factura_id . ")";
    }
    
    if (!$pago->user) {
        $problemas[] = "Usuario faltante (user_id: " . $pago->user_id . ")";
    }
    
    if ($pago->factura && !$pago->factura->cliente) {
        $problemas[] = "Cliente faltante en factura";
    }
    
    if (empty($problemas)) {
        echo "   ✅ Pago ID " . $pago->id . " - Sin problemas" . PHP_EOL;
    } else {
        echo "   ❌ Pago ID " . $pago->id . " - Problemas: " . implode(', ', $problemas) . PHP_EOL;
    }
}

// 6. Verificar usuarios administradores
echo PHP_EOL . "6. Usuarios Administradores activos:" . PHP_EOL;
$admins = User::role('Administrador')->where('is_active', true)->get();
foreach ($admins as $admin) {
    echo "   - " . $admin->name . " (" . $admin->email . ")" . PHP_EOL;
}
