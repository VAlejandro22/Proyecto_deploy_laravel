<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== Creando usuario con rol Pagos ===" . PHP_EOL;

$user = User::create([
    'name' => 'Validador de Pagos',
    'email' => 'pagos@facturacion.com',
    'password' => bcrypt('pagos123'),
    'is_active' => true,
]);

$user->assignRole('Pagos');

echo "✅ Usuario creado:" . PHP_EOL;
echo "   Nombre: " . $user->name . PHP_EOL;
echo "   Email: " . $user->email . PHP_EOL;
echo "   Contraseña: pagos123" . PHP_EOL;
echo "   Rol: Pagos" . PHP_EOL;

echo PHP_EOL . "=== Verificación ===" . PHP_EOL;
echo "Roles asignados: " . $user->roles->pluck('name')->implode(', ') . PHP_EOL;
echo "¿Tiene rol Pagos?: " . ($user->hasRole('Pagos') ? 'SI' : 'NO') . PHP_EOL;
