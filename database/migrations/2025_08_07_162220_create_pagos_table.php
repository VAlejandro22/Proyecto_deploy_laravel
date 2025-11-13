<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // Cliente que hace el pago
            $table->enum('tipo_pago', ['efectivo', 'tarjeta', 'transferencia', 'cheque']);
            $table->string('numero_transaccion');
            $table->decimal('monto_pagado', 10, 2);
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->timestamp('fecha_validacion')->nullable();
            $table->foreignId('validado_por')->nullable()->constrained('users'); // Usuario que valida
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
