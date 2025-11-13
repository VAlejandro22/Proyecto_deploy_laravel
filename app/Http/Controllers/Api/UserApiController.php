<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserApiController extends Controller
{
    /**
     * Listar todos los usuarios (Solo Administradores)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search');
            $status = $request->get('status');
            $role = $request->get('role');

            $query = User::with(['roles']);

            // Aplicar filtros
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($status !== null) {
                $query->where('activo', $status);
            }

            if ($role) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            }

            $users = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuarios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear un nuevo usuario (Solo Administradores)
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role_id' => 'required|exists:roles,id',
                'activo' => 'boolean'
            ]);

            DB::beginTransaction();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'activo' => $validated['activo'] ?? true,
                'email_verified_at' => now(),
            ]);

            $role = Role::findOrFail($validated['role_id']);
            /** @var \Spatie\Permission\Models\Role $role */
            $user->assignRole($role->name);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'data' => $user->load('roles')
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->validator->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un usuario específico (Solo Administradores)
     */
    public function show(User $user): JsonResponse
    {
        try {
            $user->load(['roles', 'tokens']);

            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un usuario (Solo Administradores)
     */
    public function update(Request $request, User $user): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'password' => 'nullable|string|min:8|confirmed',
                'role_id' => 'required|exists:roles,id',
                'activo' => 'boolean'
            ]);

            DB::beginTransaction();

            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'activo' => $validated['activo'] ?? $user->activo,
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            // Actualizar rol
            $role = Role::findOrFail($validated['role_id']);
            /** @var \Spatie\Permission\Models\Role $role */
            $user->syncRoles([$role->name]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',
                'data' => $user->load('roles')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->validator->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un usuario (Solo Administradores)
     */
    public function destroy(User $user): JsonResponse
    {
        try {
            // No permitir eliminar al propio usuario
            if (Auth::id() === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminar tu propio usuario'
                ], 403);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado del usuario (Solo Administradores)
     */
    public function toggleStatus(User $user): JsonResponse
    {
        try {
            // No permitir desactivar al propio usuario
            if (Auth::id() === $user->id && $user->activo) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes desactivar tu propio usuario'
                ], 403);
            }

            $user->update(['activo' => !$user->activo]);

            return response()->json([
                'success' => true,
                'message' => $user->activo ? 'Usuario activado' : 'Usuario desactivado',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado del usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear token de acceso para un usuario (Solo Administradores)
     */
    public function createToken(Request $request, User $user): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'abilities' => 'array',
                'abilities.*' => 'string'
            ]);

            $token = $user->createToken(
                $validated['name'],
                $validated['abilities'] ?? ['*']
            );

            return response()->json([
                'success' => true,
                'message' => 'Token creado exitosamente',
                'data' => [
                    'token' => $token->plainTextToken,
                    'token_id' => $token->accessToken->id,
                    'name' => $validated['name'],
                    'abilities' => $token->accessToken->abilities
                ]
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
                'message' => 'Error al crear token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar token de un usuario (Solo Administradores)
     */
    public function deleteToken(User $user, $tokenId): JsonResponse
    {
        try {
            $token = $user->tokens()->find($tokenId);

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token no encontrado'
                ], 404);
            }

            $token->delete();

            return response()->json([
                'success' => true,
                'message' => 'Token eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar token: ' . $e->getMessage()
            ], 500);
        }
    }
}
