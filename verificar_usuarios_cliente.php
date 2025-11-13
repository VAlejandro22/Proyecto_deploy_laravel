<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== Verificación de usuarios Cliente ===" . PHP_EOL;

$usuarios = User::with('roles')->whereIn('email', ['cliente.api@test.com', 'cliente@ejemplo.com'])->get();

foreach ($usuarios as $user) {
    echo "Usuario: " . $user->name . " (" . $user->email . ")" . PHP_EOL;
    echo "Roles: " . $user->roles->pluck('name')->implode(', ') . PHP_EOL;
    echo "Activo: " . ($user->is_active ? 'SI' : 'NO') . PHP_EOL;
    
    // Crear token para probar API
    $token = $user->createToken('API Test Token')->plainTextToken;
    echo "Token API: " . $token . PHP_EOL;
    echo "---" . PHP_EOL;
}

echo "✅ Verificación completada" . PHP_EOL;
