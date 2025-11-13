<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== Asignando rol Pagos a usuario existente ===" . PHP_EOL;

$user = User::where('email', 'pagos@facturacion.com')->first();

if ($user) {
    echo "Usuario encontrado: " . $user->name . " (" . $user->email . ")" . PHP_EOL;
    
    if (!$user->hasRole('Pagos')) {
        $user->assignRole('Pagos');
        echo "✅ Rol 'Pagos' asignado" . PHP_EOL;
    } else {
        echo "✅ El usuario ya tiene el rol 'Pagos'" . PHP_EOL;
    }
    
    echo "Roles actuales: " . $user->roles->pluck('name')->implode(', ') . PHP_EOL;
    echo "Estado: " . ($user->is_active ? 'Activo' : 'Inactivo') . PHP_EOL;
} else {
    echo "❌ Usuario no encontrado. Creando uno nuevo..." . PHP_EOL;
    
    $user = User::create([
        'name' => 'Validador de Pagos',
        'email' => 'pagos.validador@facturacion.com',
        'password' => bcrypt('pagos123'),
        'is_active' => true,
    ]);
    
    $user->assignRole('Pagos');
    
    echo "✅ Usuario creado:" . PHP_EOL;
    echo "   Email: " . $user->email . PHP_EOL;
    echo "   Contraseña: pagos123" . PHP_EOL;
}
