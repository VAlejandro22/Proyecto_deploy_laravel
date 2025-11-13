<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Factura;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;

class ResetClientes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:clientes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Eliminar todos los clientes y datos relacionados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirm('¿Estás seguro de que quieres eliminar TODOS los clientes y sus datos relacionados? Esta acción no se puede deshacer.')) {
            $this->info('Operación cancelada.');
            return 0;
        }

        try {
            DB::beginTransaction();

            // Contar registros antes
            $clientesCount = Cliente::count();
            $facturasCount = Factura::count();
            $pagosCount = Pago::count();
            $usuariosClienteCount = User::role('Cliente')->count();

            $this->info("Registros encontrados:");
            $this->info("- Clientes: {$clientesCount}");
            $this->info("- Facturas: {$facturasCount}");
            $this->info("- Pagos: {$pagosCount}");
            $this->info("- Usuarios Cliente: {$usuariosClienteCount}");

            // Eliminar en orden correcto (por las relaciones)
            $this->info("Eliminando pagos...");
            Pago::truncate();

            $this->info("Eliminando facturas...");
            Factura::truncate();

            $this->info("Eliminando usuarios con rol Cliente...");
            $usuariosCliente = User::role('Cliente')->get();
            foreach ($usuariosCliente as $usuario) {
                $usuario->roles()->detach();
                $usuario->delete();
            }

            $this->info("Eliminando clientes...");
            Cliente::truncate();

            DB::commit();

            $this->info("✅ Todos los clientes y datos relacionados han sido eliminados exitosamente.");
            $this->info("Ahora puedes crear nuevos clientes desde cero.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error al eliminar clientes: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
