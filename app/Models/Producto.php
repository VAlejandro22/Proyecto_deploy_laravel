<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria',
        'codigo',
        'stock',
        'precio',
        'is_active'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relación con facturas (many-to-many)
     */
    public function facturas()
    {
        return $this->belongsToMany(Factura::class, 'factura_producto')
                    ->withPivot('cantidad', 'precio_unitario')
                    ->withTimestamps();
    }

    /**
     * Scope para productos activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para productos con stock
     */
    public function scopeWithStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Accessor para 'activo' (compatibilidad)
     */
    public function getActivoAttribute()
    {
        return $this->is_active;
    }

    /**
     * Mutator para 'activo' (compatibilidad)
     */
    public function setActivoAttribute($value)
    {
        $this->attributes['is_active'] = $value;
    }

    /**
     * Scope para productos con stock bajo (menos de 10 unidades)
     */
    public function scopeLowStock($query)
    {
        return $query->where('stock', '<', 10);
    }
}
