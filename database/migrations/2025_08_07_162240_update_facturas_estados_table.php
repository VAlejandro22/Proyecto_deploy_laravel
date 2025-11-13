<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Para PostgreSQL, necesitamos primero eliminar la restricción existente
        DB::statement("ALTER TABLE facturas DROP CONSTRAINT IF EXISTS facturas_estado_check");
        
        // Luego agregar la nueva restricción con los estados adicionales
        DB::statement("ALTER TABLE facturas ADD CONSTRAINT facturas_estado_check CHECK (estado IN ('pendiente', 'pagada', 'anulada', 'activa'))");
        
        // Cambiar el valor por defecto
        DB::statement("ALTER TABLE facturas ALTER COLUMN estado SET DEFAULT 'pendiente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir al constraint original
        DB::statement("ALTER TABLE facturas DROP CONSTRAINT IF EXISTS facturas_estado_check");
        DB::statement("ALTER TABLE facturas ADD CONSTRAINT facturas_estado_check CHECK (estado IN ('activa', 'anulada'))");
        DB::statement("ALTER TABLE facturas ALTER COLUMN estado SET DEFAULT 'activa'");
    }
};
