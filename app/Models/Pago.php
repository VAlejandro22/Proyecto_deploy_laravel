<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'factura_id',
        'user_id',
        'tipo_pago',
        'numero_transaccion',
        'monto_pagado',
        'observaciones',
        'estado',
        'fecha_validacion',
        'validado_por'
    ];

    protected $casts = [
        'monto_pagado' => 'decimal:2',
        'fecha_validacion' => 'datetime',
    ];

    /**
     * Relación con la factura
     */
    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    /**
     * Relación con el usuario que realizó el pago (cliente)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el usuario que validó el pago
     */
    public function validador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validado_por');
    }

    /**
     * Scope para pagos pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope para pagos aprobados
     */
    public function scopeAprobados($query)
    {
        return $query->where('estado', 'aprobado');
    }

    /**
     * Scope para pagos rechazados
     */
    public function scopeRechazados($query)
    {
        return $query->where('estado', 'rechazado');
    }
}
