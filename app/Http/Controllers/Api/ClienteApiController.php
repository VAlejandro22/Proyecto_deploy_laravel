<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ClienteApiController extends Controller
{
    /**
     * Listar todos los clientes
     * Acceso: Administrador|Secretario|Ventas
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search');
            $status = $request->get('status');

            $query = Cliente::query();

            // Aplicar filtros
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('telefono', 'like', "%{$search}%")
                      ->orWhere('nit', 'like', "%{$search}%");
                });
            }

            if ($status !== null) {
                $query->where('activo', $status);
            }

            $clientes = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $clientes->items(),
                'pagination' => [
                    'current_page' => $clientes->currentPage(),
                    'last_page' => $clientes->lastPage(),
                    'per_page' => $clientes->perPage(),
                    'total' => $clientes->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener clientes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear un nuevo cliente
     * Acceso: Administrador|Secretario
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'email' => 'required|email|unique:clientes,email',
                'telefono' => 'nullable|string|max:20',
                'direccion' => 'nullable|string|max:500',
                'nit' => 'nullable|string|max:20|unique:clientes,nit',
                'activo' => 'boolean'
            ]);

            $cliente = Cliente::create([
                'nombre' => $validated['nombre'],
                'email' => $validated['email'],
                'telefono' => $validated['telefono'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
                'nit' => $validated['nit'] ?? null,
                'activo' => $validated['activo'] ?? true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cliente creado exitosamente',
                'data' => $cliente
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->validator->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un cliente específico
     * Acceso: Administrador|Secretario|Ventas
     */
    public function show(Cliente $cliente): JsonResponse
    {
        try {
            $cliente->load(['facturas', 'user', 'user.roles']);

            return response()->json([
                'success' => true,
                'data' => $cliente
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un cliente
     * Acceso: Administrador|Secretario
     */
    public function update(Request $request, Cliente $cliente): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'email' => ['required', 'email', Rule::unique('clientes')->ignore($cliente->id)],
                'telefono' => 'nullable|string|max:20',
                'direccion' => 'nullable|string|max:500',
                'nit' => ['nullable', 'string', 'max:20', Rule::unique('clientes')->ignore($cliente->id)],
                'activo' => 'boolean'
            ]);

            $cliente->update([
                'nombre' => $validated['nombre'],
                'email' => $validated['email'],
                'telefono' => $validated['telefono'] ?? $cliente->telefono,
                'direccion' => $validated['direccion'] ?? $cliente->direccion,
                'nit' => $validated['nit'] ?? $cliente->nit,
                'activo' => $validated['activo'] ?? $cliente->activo,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cliente actualizado exitosamente',
                'data' => $cliente
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->validator->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un cliente
     * Acceso: Solo Administrador
     */
    public function destroy(Cliente $cliente): JsonResponse
    {
        try {
            // Verificar si el cliente tiene facturas
            if ($cliente->facturas()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el cliente porque tiene facturas asociadas'
                ], 409);
            }

            $cliente->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cliente eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado del cliente
     * Acceso: Administrador|Secretario
     */
    public function toggleStatus(Cliente $cliente): JsonResponse
    {
        try {
            $cliente->update(['activo' => !$cliente->activo]);

            return response()->json([
                'success' => true,
                'message' => $cliente->activo ? 'Cliente activado' : 'Cliente desactivado',
                'data' => $cliente
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado del cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener roles disponibles para asignar a clientes
     * Acceso: Solo Administrador
     */
    public function getRoles(): JsonResponse
    {
        try {
            $roles = Role::all();

            return response()->json([
                'success' => true,
                'data' => $roles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener roles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Asignar rol a un cliente
     * Acceso: Solo Administrador
     */
    public function assignRole(Request $request, Cliente $cliente): JsonResponse
    {
        try {
            $validated = $request->validate([
                'role_id' => 'required|exists:roles,id'
            ]);

            if (!$cliente->user) {
                return response()->json([
                    'success' => false,
                    'message' => 'El cliente no tiene un usuario asociado'
                ], 400);
            }

            $role = Role::findOrFail($validated['role_id']);
            /** @var User $user */
            $user = $cliente->user;
            $user->syncRoles([$role->name]);

            return response()->json([
                'success' => true,
                'message' => 'Rol asignado exitosamente',
                'data' => $cliente->load(['user', 'user.roles'])
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->validator->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar rol: ' . $e->getMessage()
            ], 500);
        }
    }
}
