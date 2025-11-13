<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

// class User extends Authenticatable implements MustVerifyEmail
class User extends Authenticatable 
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
       'name', 'email', 'password', 'is_active',
    ];

    protected $casts = [
       'email_verified_at' => 'datetime',
       'password' => 'hashed',
       'is_active'         => 'boolean',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relación con facturas creadas
     */
    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    /**
     * Relación con cliente (si el usuario es un cliente)
     */
    public function cliente(): HasOne
    {
        return $this->hasOne(Cliente::class);
    }

    /**
     * Scope para usuarios activos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
}
