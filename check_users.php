<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

echo "🔍 Checking admin user status...\n\n";

// Check all users
$users = User::with('roles')->get();

echo "All users in the system:\n";
echo "=" . str_repeat("=", 50) . "\n";

foreach ($users as $user) {
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Roles: " . ($user->roles->isEmpty() ? 'NO ROLES ASSIGNED' : $user->roles->pluck('name')->join(', ')) . "\n";
    echo "Is Active: " . ($user->is_active ? 'Yes' : 'No') . "\n";
    echo "Email Verified: " . ($user->email_verified_at ? 'Yes' : 'No') . "\n";
    echo "Created: " . $user->created_at->format('Y-m-d H:i:s') . "\n";
    echo "-" . str_repeat("-", 50) . "\n";
}

// Check all roles
echo "\nAll roles in the system:\n";
echo "=" . str_repeat("=", 50) . "\n";

$roles = Role::with('permissions')->get();
foreach ($roles as $role) {
    echo "Role: {$role->name}\n";
    echo "Permissions: " . $role->permissions->pluck('name')->join(', ') . "\n";
    echo "Users: " . $role->users->pluck('name')->join(', ') . "\n";
    echo "-" . str_repeat("-", 50) . "\n";
}

// Fix admin email verification
echo "\n🛠️ Verifying admin users emails...\n";
echo "=" . str_repeat("=", 50) . "\n";

$adminUsers = User::role('Administrador')->get();

foreach ($adminUsers as $user) {
    echo "Admin: {$user->name} ({$user->email})\n";
    
    if (!$user->email_verified_at) {
        // Marcar el email como verificado
        $user->email_verified_at = now();
        $user->save();
        echo "  ✅ Email verified automatically\n";
    } else {
        echo "  ✓ Email already verified\n";
    }
}

echo "\n✅ Check and fixes completed!\n";
