<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiTokenController extends Controller
{
    /**
     * Crear un token de API para un cliente
     */
    public function createTokenForClient(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'nombre_token' => 'required|string|max:255',
        ]);

        try {
            $cliente = Cliente::findOrFail($request->cliente_id);
            
            // Si el cliente no tiene usuario asociado, crear uno
            if (!$cliente->user_id) {
                $user = User::create([
                    'name' => $cliente->nombre,
                    'email' => $cliente->email,
                    'password' => Hash::make('temp_password_' . time()),
                    'is_active' => true,
                    'email_verified_at' => now()
                ]);
                
                // Asignar rol de cliente si existe
                try {
                    // Verificar si el rol existe antes de asignarlo
                    $clienteRole = \Spatie\Permission\Models\Role::where('name', 'Cliente')->first();
                    if ($clienteRole && !$user->hasRole('Cliente')) {
                        $user->assignRole('Cliente');
                    }
                } catch (\Exception $e) {
                    // Rol no existe, continuar sin asignar
                }
                
                $cliente->update(['user_id' => $user->id]);
            } else {
                $user = User::find($cliente->user_id);
                if (!$user) {
                    throw new \Exception('Usuario asociado al cliente no encontrado');
                }
            }

            // Crear el token
            $token = $user->createToken($request->nombre_token);

            return redirect()->back()->with('token_created', [
                'name' => $request->nombre_token,
                'token' => $token->plainTextToken,
                'cliente' => $cliente->nombre
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear el token: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un token de API
     */
    public function deleteToken(Request $request, $clienteId, $tokenId)
    {
        try {
            $cliente = Cliente::findOrFail($clienteId);
            
            if ($cliente->user_id) {
                $user = User::find($cliente->user_id);
                if ($user) {
                    $user->tokens()->where('id', $tokenId)->delete();
                }
            }

            return redirect()->back()->with('success', 'Token eliminado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el token: ' . $e->getMessage());
        }
    }

    /**
     * Obtener todos los clientes con sus tokens para el dashboard
     */
    public function index()
    {
        $clientes = Cliente::with(['user.tokens'])->whereNotNull('user_id')->orderBy('nombre')->get();
        
        return view('dashboard.api-tokens', compact('clientes'));
    }
}
