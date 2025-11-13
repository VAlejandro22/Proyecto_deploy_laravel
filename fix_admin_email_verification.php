<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

echo "🔧 Verificando emails de administradores...\n\n";

// Reset cached roles and permissions
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

// Encontrar todos los usuarios con rol de administrador
$adminUsers = User::role('Administrador')->get();

echo "Administradores encontrados: " . $adminUsers->count() . "\n";
echo "=" . str_repeat("=", 50) . "\n";

foreach ($adminUsers as $user) {
    echo "Usuario: {$user->name} ({$user->email})\n";
    
    if (!$user->email_verified_at) {
        // Marcar el email como verificado
        $user->email_verified_at = now();
        $user->save();
        echo "  ✅ Email verificado automáticamente\n";
    } else {
        echo "  ✓ Email ya estaba verificado\n";
    }
    echo "-" . str_repeat("-", 50) . "\n";
}

echo "\n✅ Proceso completado!\n";
echo "Todos los administradores pueden ahora acceder al sistema sin verificar su email.\n";
echo "Los nuevos administradores que se creen también tendrán su email verificado automáticamente.\n";
