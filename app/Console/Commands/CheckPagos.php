<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pago;

class CheckPagos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:pagos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check pagos in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pagos = Pago::with(['factura.cliente', 'user'])->get();
        
        $this->info("Total pagos en base de datos: " . $pagos->count());
        
        if ($pagos->count() > 0) {
            $this->table(
                ['ID', 'Estado', 'Monto', 'Cliente', 'Usuario', 'Fecha'],
                $pagos->map(function($pago) {
                    return [
                        $pago->id,
                        $pago->estado,
                        '$' . number_format((float)$pago->monto_pagado, 2),
                        $pago->factura->cliente->nombre ?? 'Sin cliente',
                        $pago->user->name ?? 'Sin usuario',
                        $pago->created_at->format('d/m/Y H:i')
                    ];
                })->toArray()
            );
            
            $pendientes = $pagos->where('estado', 'pendiente')->count();
            $aprobados = $pagos->where('estado', 'aprobado')->count();
            $rechazados = $pagos->where('estado', 'rechazado')->count();
            
            $this->info("Pendientes: $pendientes");
            $this->info("Aprobados: $aprobados");
            $this->info("Rechazados: $rechazados");
        }
        
        return 0;
    }
}
