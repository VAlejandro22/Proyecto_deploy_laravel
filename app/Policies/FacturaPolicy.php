<?php

namespace App\Policies;

use App\Models\Factura;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class FacturaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_facturas');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Factura $factura): bool
    {
        if ($user->hasPermissionTo('view_facturas')) {
            // Los administradores pueden ver todas las facturas
            if ($user->hasRole('Administrador')) {
                return true;
            }
            
            // Los demás solo pueden ver sus propias facturas
            return $user->id === $factura->user_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_facturas');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Factura $factura): bool
    {
        if ($user->hasPermissionTo('edit_facturas')) {
            // Solo se pueden editar facturas activas
            if ($factura->estado !== 'activa') {
                return false;
            }
            
            // Los administradores pueden editar todas las facturas
            if ($user->hasRole('Administrador')) {
                return true;
            }
            
            // Los demás solo pueden editar sus propias facturas
            return $user->id === $factura->user_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Factura $factura): bool
    {
        if ($user->hasPermissionTo('delete_facturas')) {
            // Los administradores pueden eliminar cualquier factura
            if ($user->hasRole('Administrador')) {
                return true;
            }
            
            // Los demás solo pueden eliminar sus propias facturas
            return $user->id === $factura->user_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can anular the model.
     */
    public function anular(User $user, Factura $factura): bool
    {
        if ($user->hasPermissionTo('anular_facturas')) {
            // Solo se pueden anular facturas activas
            if ($factura->estado !== 'activa') {
                return false;
            }
            
            // Los administradores pueden anular cualquier factura
            if ($user->hasRole('Administrador')) {
                return true;
            }
            
            // Los demás solo pueden anular sus propias facturas
            return $user->id === $factura->user_id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Factura $factura): bool
    {
        return $user->hasRole('Administrador');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Factura $factura): bool
    {
        return $user->hasRole('Administrador');
    }
}
