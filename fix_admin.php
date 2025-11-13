<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "🔧 Fixing admin roles and permissions...\n";

// Reset cached roles and permissions
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

// Create permissions if they don't exist
$permissions = [
    'gestionar-usuarios',
    'gestionar-clientes', 
    'ver-clientes',
    'gestionar-productos',
    'ver-productos',
    'gestionar-facturas',
    'ver-facturas',
    'gestionar-reportes',
    'ver-reportes',
];

echo "Creating permissions...\n";
foreach ($permissions as $permission) {
    Permission::firstOrCreate(['name' => $permission]);
    echo "  ✓ $permission\n";
}

// Create admin role
echo "Creating admin role...\n";
$adminRole = Role::firstOrCreate(['name' => 'Administrador']);
$adminRole->syncPermissions($permissions); // Give all permissions to admin
echo "  ✓ Administrador role created with all permissions\n";

// Create other roles
$secretarioRole = Role::firstOrCreate(['name' => 'Secretario']);
$bodegaRole = Role::firstOrCreate(['name' => 'Bodega']);
$ventasRole = Role::firstOrCreate(['name' => 'Ventas']);

// Find admin users
$adminUsers = User::whereIn('email', ['admin@empresa.com', 'admin@facturacion.com'])->get();

echo "Assigning admin role to users...\n";
foreach ($adminUsers as $user) {
    if (!$user->hasRole('Administrador')) {
        $user->assignRole($adminRole);
        echo "  ✓ Role assigned to {$user->email}\n";
    } else {
        echo "  - {$user->email} already has admin role\n";
    }
}

// Verify
echo "\nVerification:\n";
foreach ($adminUsers as $user) {
    echo "User: {$user->name} ({$user->email})\n";
    echo "  Roles: " . $user->roles->pluck('name')->join(', ') . "\n";
    echo "  Permissions: " . $user->getAllPermissions()->pluck('name')->join(', ') . "\n\n";
}

echo "✅ Admin setup completed!\n";
