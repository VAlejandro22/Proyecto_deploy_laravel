<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

echo "=== Verificación de Roles de Pagos ===" . PHP_EOL;

// Verificar si existe el rol Pagos
$rolePagos = Role::where('name', 'Pagos')->first();
if ($rolePagos) {
    echo "✅ Rol 'Pagos' encontrado" . PHP_EOL;
} else {
    echo "❌ Rol 'Pagos' NO encontrado" . PHP_EOL;
}

echo PHP_EOL . "=== Usuarios con rol Administrador ===" . PHP_EOL;
$admins = User::role('Administrador')->get();
foreach ($admins as $admin) {
    echo "- " . $admin->name . " (" . $admin->email . ") - Activo: " . ($admin->is_active ? 'SI' : 'NO') . PHP_EOL;
}

echo PHP_EOL . "=== Usuarios con rol Pagos ===" . PHP_EOL;
$pagosUsers = User::role('Pagos')->get();
if ($pagosUsers->count() > 0) {
    foreach ($pagosUsers as $pagoUser) {
        echo "- " . $pagoUser->name . " (" . $pagoUser->email . ") - Activo: " . ($pagoUser->is_active ? 'SI' : 'NO') . PHP_EOL;
    }
} else {
    echo "❌ No hay usuarios con rol 'Pagos'" . PHP_EOL;
}

echo PHP_EOL . "=== Todos los roles disponibles ===" . PHP_EOL;
$roles = Role::all();
foreach ($roles as $role) {
    $userCount = User::role($role->name)->count();
    echo "- " . $role->name . " (Usuarios: " . $userCount . ")" . PHP_EOL;
}
