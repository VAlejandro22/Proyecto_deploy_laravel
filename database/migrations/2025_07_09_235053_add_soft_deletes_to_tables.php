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
        // Agregar soft deletes a la tabla clientes
        Schema::table('clientes', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Agregar soft deletes a la tabla productos
        Schema::table('productos', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Agregar soft deletes a la tabla facturas
        Schema::table('facturas', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('facturas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
