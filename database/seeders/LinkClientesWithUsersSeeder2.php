<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class LinkClientesWithUsersSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $this->command->info('Iniciando proceso de vinculación de clientes con usuarios...');
        $this->command->newLine();

        // Obtener todos los clientes que no tienen usuario asociado
        $clientesSinUsuario = Cliente::whereNull('user_id')->get();
        
        $this->command->info("Clientes encontrados sin usuario: {$clientesSinUsuario->count()}");
        
        if ($clientesSinUsuario->count() === 0) {
            $this->command->info('Todos los clientes ya tienen usuarios asociados.');
            return;
        }

        $this->command->newLine();
        $creados = 0;
        $vinculados = 0;
        $errores = 0;

        foreach ($clientesSinUsuario as $cliente) {
            try {
                DB::beginTransaction();

                $this->command->info("Procesando cliente: {$cliente->nombre} ({$cliente->email})");

                // Verificar si ya existe un usuario con ese email
                $usuarioExistente = User::where('email', $cliente->email)->first();

                if ($usuarioExistente) {
                    // Si existe un usuario con ese email, vincularlo con el cliente
                    $cliente->update(['user_id' => $usuarioExistente->id]);
                    $vinculados++;
                    $this->command->info("  ✓ Usuario existente vinculado");
                    
                    // Asignar rol de cliente si existe el sistema de roles
                    $this->asignarRolCliente($usuarioExistente);
                    
                } else {
                    // Crear nuevo usuario para el cliente
                    $nuevoUsuario = User::create([
                        'name' => $cliente->nombre,
                        'email' => $cliente->email,
                        'password' => Hash::make('cliente_' . $cliente->id . '_' . time()),
                        'is_active' => true,
                        'email_verified_at' => now()
                    ]);

                    // Vincular el cliente con el nuevo usuario
                    $cliente->update(['user_id' => $nuevoUsuario->id]);
                    $creados++;
                    $this->command->info("  ✓ Nuevo usuario creado y vinculado");
                    
                    // Asignar rol de cliente si existe el sistema de roles
                    $this->asignarRolCliente($nuevoUsuario);
                }

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $errores++;
                $this->command->error("  ✗ Error procesando cliente {$cliente->nombre}: " . $e->getMessage());
            }
        }

        $this->command->newLine();
        $this->command->info('=== Resumen del proceso ===');
        $this->command->info("Usuarios nuevos creados: {$creados}");
        $this->command->info("Usuarios existentes vinculados: {$vinculados}");
        $this->command->info("Errores: {$errores}");
        $this->command->info("Total procesados: " . ($creados + $vinculados + $errores));
        
        if ($errores > 0) {
            $this->command->warn("Se encontraron {$errores} errores durante el proceso.");
        }
        
        if ($creados > 0 || $vinculados > 0) {
            $this->command->info('¡Proceso completado exitosamente!');
            $this->command->info('Ahora puedes crear tokens API para todos los clientes desde el dashboard del administrador.');
        }
    }

    /**
     * Asignar rol de cliente si el sistema de roles está disponible
     */
    private function asignarRolCliente(User $user): void
    {
        try {
            // Verificar si el sistema de Spatie Permission está disponible
            if (class_exists(\Spatie\Permission\Models\Role::class) && method_exists($user, 'hasRole')) {
                
                // Verificar si el rol Cliente existe
                $rolCliente = \Spatie\Permission\Models\Role::where('name', 'Cliente')->first();
                
                if ($rolCliente && !$user->hasRole('Cliente')) {
                    $user->assignRole('Cliente');
                    $this->command->info("    → Rol 'Cliente' asignado");
                } elseif (!$rolCliente) {
                    // Crear el rol Cliente si no existe
                    \Spatie\Permission\Models\Role::create(['name' => 'Cliente']);
                    $user->assignRole('Cliente');
                    $this->command->info("    → Rol 'Cliente' creado y asignado");
                }
            }
        } catch (\Exception $e) {
            // Si hay error con los roles, continuar sin asignar (no es crítico)
            $this->command->warn("    ⚠ No se pudo asignar rol: " . $e->getMessage());
        }
    }
}
