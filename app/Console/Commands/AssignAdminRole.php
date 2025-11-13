<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssignAdminRole extends Command
{
    protected $signature = 'admin:assign-role {email}';
    protected $description = 'Assign admin role to a user';

    public function handle()
    {
        $email = $this->argument('email');
        
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found");
            return 1;
        }
        
        // Create permissions
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

        $this->info('Creating permissions...');
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        
        // Create admin role
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $adminRole->syncPermissions($permissions);
        
        // Assign role to user
        if (!$user->hasRole('Administrador')) {
            $user->assignRole($adminRole);
            $this->info("Admin role assigned to {$user->name} ({$user->email})");
        } else {
            $this->info("User {$user->name} already has admin role");
        }
        
        // Verify
        $this->info("Current roles: " . $user->roles->pluck('name')->join(', '));
        $this->info("Current permissions: " . $user->getAllPermissions()->pluck('name')->join(', '));
        
        return 0;
    }
}
