<?php

namespace App\Console\Commands;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MigrateClientesExistentes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clientes:migrate-users
                          {--dry-run : Solo mostrar qué se haría sin hacer cambios}
                          {--password= : Contraseña por defecto (opcional, se generará aleatoria si no se especifica)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear usuarios para clientes existentes que no tienen usuario asociado';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $defaultPassword = $this->option('password') ?: Str::random(12);
        
        $this->info('🔍 Buscando clientes sin usuario asociado...');
        
        $clientesSinUsuario = Cliente::whereNull('user_id')
            ->orWhereDoesntHave('user')
            ->get();
            
        if ($clientesSinUsuario->isEmpty()) {
            $this->info('✅ Todos los clientes ya tienen usuarios asociados.');
            return;
        }
        
        $this->info("📊 Encontrados {$clientesSinUsuario->count()} clientes sin usuario.");
        
        if ($dryRun) {
            $this->warn('🔍 MODO DRY-RUN: No se realizarán cambios');
            $this->table(
                ['ID', 'Nombre', 'Email', 'Estado'],
                $clientesSinUsuario->map(function($cliente) {
                    return [
                        $cliente->id,
                        $cliente->nombre,
                        $cliente->email,
                        $cliente->is_active ? 'Activo' : 'Inactivo'
                    ];
                })
            );
            return;
        }
        
        $this->info('🚀 Iniciando migración...');
        $creados = 0;
        $errores = 0;
        
        foreach ($clientesSinUsuario as $cliente) {
            try {
                DB::beginTransaction();
                
                // Verificar si ya existe un usuario con ese email
                $existingUser = User::where('email', $cliente->email)->first();
                
                if ($existingUser) {
                    // Si existe, asociarlo al cliente
                    $cliente->update(['user_id' => $existingUser->id]);
                    
                    // Asignar rol Cliente si no lo tiene
                    if (!$existingUser->hasRole('Cliente')) {
                        $existingUser->assignRole('Cliente');
                        $this->info("✅ Usuario existente '{$existingUser->email}' asociado y rol Cliente asignado");
                    } else {
                        $this->info("✅ Usuario existente '{$existingUser->email}' asociado");
                    }
                } else {
                    // Crear nuevo usuario
                    $user = User::create([
                        'name' => $cliente->nombre,
                        'email' => $cliente->email,
                        'password' => bcrypt($defaultPassword),
                        'is_active' => $cliente->is_active,
                    ]);
                    
                    // Asignar rol Cliente
                    $user->assignRole('Cliente');
                    
                    // Asociar al cliente
                    $cliente->update(['user_id' => $user->id]);
                    
                    $this->info("✅ Usuario creado para '{$cliente->nombre}' ({$cliente->email}) - Contraseña: {$defaultPassword}");
                }
                
                DB::commit();
                $creados++;
                
            } catch (\Exception $e) {
                DB::rollback();
                $this->error("❌ Error procesando cliente '{$cliente->nombre}': " . $e->getMessage());
                $errores++;
            }
        }
        
        $this->info("🎉 Migración completada:");
        $this->info("   ✅ Usuarios creados/asociados: {$creados}");
        if ($errores > 0) {
            $this->warn("   ❌ Errores: {$errores}");
        }
        
        if ($creados > 0 && !$this->option('password')) {
            $this->warn("⚠️  Se ha usado la contraseña por defecto: {$defaultPassword}");
            $this->warn("   Se recomienda que los usuarios cambien su contraseña al primer acceso.");
        }
    }
}
