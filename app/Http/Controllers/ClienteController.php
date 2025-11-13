<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class ClienteController extends Controller
{
    // En Laravel 12, el middleware se define en las rutas, no en el constructor del controlador

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cliente::with(['facturas', 'user']);
        
        // Filtro de búsqueda
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telefono', 'like', "%{$search}%");
            });
        }
        
        // Filtro de estado
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }
        
        $clientes = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('clientes.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {        
        return view('clientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|min:2',
            'email' => 'required|email|unique:clientes,email|unique:users,email|max:255',
            'telefono' => 'nullable|string|max:20|min:7',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();
            
            // 1. Crear el usuario con rol Cliente
            $user = User::create([
                'name' => $validated['nombre'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'is_active' => true,
            ]);
            
            // 2. Asignar rol Cliente
            $user->assignRole('Cliente');
            
            // 3. Crear el cliente y vincularlo con el usuario
            $cliente = Cliente::create([
                'nombre' => $validated['nombre'],
                'email' => $validated['email'],
                'telefono' => $validated['telefono'],
                'user_id' => $user->id,
                'is_active' => true,
            ]);
            
            DB::commit();
            
            return redirect()->route('clientes.index')
                ->with('success', 'Cliente y usuario creados exitosamente. El cliente puede acceder al sistema con su email y contraseña.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {        
        $facturas = $cliente->facturas()
            ->with('productos')
            ->orderBy('created_at', 'desc')
            ->paginate(5);
            
        return view('clientes.show', ['cliente' => $cliente, 'facturas' => $facturas]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {        
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|min:2',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('clientes')->ignore($cliente->id),
                Rule::unique('users')->ignore($cliente->user_id ?? null)
            ],
            'telefono' => 'nullable|string|max:20|min:7',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();
            
            // Actualizar el cliente
            $cliente->update($validated);
            
            // Si el cliente tiene un usuario asociado, actualizar también sus datos
            if ($cliente->user) {
                $cliente->user->update([
                    'name' => $validated['nombre'],
                    'email' => $validated['email'],
                    'is_active' => $validated['is_active'] ?? $cliente->is_active,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('clientes.index')
                ->with('success', 'Cliente actualizado exitosamente.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {        
        // Verificar si tiene facturas activas
        if ($cliente->facturas()->where('estado', 'activa')->count() > 0) {
            return redirect()->route('clientes.index')
                ->with('error', 'No se puede eliminar el cliente porque tiene facturas activas.');
        }
        
        try {
            DB::beginTransaction();
            
            // Si el cliente tiene un usuario asociado, desactivarlo
            if ($cliente->user) {
                $cliente->user->update(['is_active' => false]);
            }
            
            $cliente->delete();
            
            DB::commit();
            
            return redirect()->route('clientes.index')
                ->with('success', 'Cliente eliminado exitosamente. El usuario asociado ha sido desactivado.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('clientes.index')
                ->with('error', 'Error al eliminar el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario para asignar roles (solo administradores)
     */
    public function roles(Cliente $cliente)
    {        
        $roles = Role::all();
        $users = User::where('is_active', true)->get();
        
        return view('clientes.roles', ['cliente' => $cliente, 'roles' => $roles, 'users' => $users]);
    }

    /**
     * Asignar roles a un usuario cliente
     */
    public function assignRole(Request $request, Cliente $cliente)
    {        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);
        
        $user = User::findOrFail($validated['user_id']);
        
        // Sincronizar roles
        $user->syncRoles($validated['roles']);
        
        return redirect()->route('clientes.index')
            ->with('success', 'Roles asignados exitosamente.');
    }

    /**
     * Cambiar estado del cliente
     */
    public function toggleStatus(Cliente $cliente)
    {        
        try {
            DB::beginTransaction();
            
            $newStatus = !$cliente->is_active;
            
            $cliente->update([
                'is_active' => $newStatus
            ]);
            
            // Sincronizar el estado con el usuario asociado
            if ($cliente->user) {
                $cliente->user->update(['is_active' => $newStatus]);
            }
            
            DB::commit();
            
            $status = $cliente->is_active ? 'activado' : 'desactivado';
            
            return redirect()->route('clientes.index')
                ->with('success', "Cliente {$status} exitosamente. El acceso del usuario también ha sido {$status}.");
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('clientes.index')
                ->with('error', 'Error al cambiar el estado del cliente: ' . $e->getMessage());
        }
    }
}
