<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        // Verificar que sea administrador
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'No tienes permisos para gestionar usuarios.');
        }
        
        $users = User::with('roles')->paginate(10);
        
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        // Verificar que sea administrador
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'No tienes permisos para crear usuarios.');
        }
        
        $roles = Role::all();
        
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        // Verificar que sea administrador
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'No tienes permisos para crear usuarios.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,id'],
            'is_active' => ['boolean'],
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Asignar rol
            $role = Role::findById($request->role);
            $user->assignRole($role);

            // Si el rol asignado es Administrador, marcar el email como verificado automáticamente
            if ($role->name === 'Administrador') {
                $user->email_verified_at = now();
                $user->save();
            }

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'Usuario creado exitosamente.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        // Verificar que sea administrador
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'No tienes permisos para ver usuarios.');
        }
        
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // Verificar que sea administrador
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'No tienes permisos para editar usuarios.');
        }
        
        $roles = Role::all();
        
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // Verificar que sea administrador
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'No tienes permisos para actualizar usuarios.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,id'],
            'is_active' => ['boolean'],
        ]);

        try {
            DB::beginTransaction();

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Actualizar contraseña si se proporciona
            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            // Actualizar rol
            $role = Role::findById($request->role);
            $user->syncRoles([$role]);

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'Usuario actualizado exitosamente.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Verificar que sea administrador
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'No tienes permisos para eliminar usuarios.');
        }

        // No permitir eliminar al propio usuario
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // No permitir eliminar al último administrador
        $adminRole = Role::where('name', 'Administrador')->first();
        $adminCount = User::role('Administrador')->count();
        
        if ($user->hasRole('Administrador') && $adminCount <= 1) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes eliminar al último administrador del sistema.');
        }

        try {
            DB::beginTransaction();
            
            $user->delete();
            
            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'Usuario eliminado exitosamente.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('users.index')
                ->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user status (active/inactive).
     */
    public function toggleStatus(User $user)
    {
        // Verificar que sea administrador
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'No tienes permisos para cambiar el estado de usuarios.');
        }

        // No permitir desactivar al propio usuario
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        try {
            DB::beginTransaction();
            
            $user->update([
                'is_active' => !$user->is_active,
            ]);
            
            DB::commit();

            $status = $user->is_active ? 'activado' : 'desactivado';

            return redirect()->route('users.index')
                ->with('success', "Usuario {$status} exitosamente.");
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('users.index')
                ->with('error', 'Error al cambiar el estado del usuario: ' . $e->getMessage());
        }
    }

    /**
     * Create access token for a user.
     */
    public function deleteToken(User $user, $tokenId)
    {
        // Verificar que sea administrador
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'No tienes permisos para eliminar tokens de acceso.');
        }

        try {
            DB::beginTransaction();
            
            // Buscar y eliminar el token
            $user->tokens()->where('id', $tokenId)->delete();
            
            DB::commit();
            
            return redirect()->route('dashboard')
                ->with('success', 'Token eliminado exitosamente.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('dashboard')
                ->with('error', 'Error al eliminar el token: ' . $e->getMessage());
        }
    }

    public function crearTokenAcceso(Request $request)
    {
        // Verificar que sea administrador
        if (!Auth::user()->hasRole('Administrador')) {
            abort(403, 'No tienes permisos para crear tokens de acceso.');
        }

        // Validar los datos de entrada
        $request->validate([
            'user' => ['required', 'exists:users,id'],
            'nombre_token' => ['required', 'string', 'max:255'],
        ]);

        try {
            DB::beginTransaction();

            $user = User::find($request->user);
            
            // Verificar que el usuario existe
            if (!$user) {
                return redirect()->back()
                    ->with('error', 'Usuario no encontrado.');
            }

            // Verificar que el usuario esté activo
            if (!$user->is_active) {
                return redirect()->back()
                    ->with('error', 'No se puede crear un token para un usuario inactivo.');
            }

            $token = $user->createToken($request->nombre_token);
            
            // Guardar el token en la sesión para mostrarlo una única vez
            session()->flash('token_created', [
                'token' => $token->plainTextToken,
                'name' => $request->nombre_token
            ]);
            
            DB::commit();

            return redirect()->route('dashboard')
                ->with('success', 'Token de acceso creado exitosamente.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Error al crear el token: ' . $e->getMessage());
        }
    }
}


