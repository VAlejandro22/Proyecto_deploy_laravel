<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Laravel\Sanctum\PersonalAccessToken;

class ApiTokenApiController extends Controller
{
    /**
     * Listar todos los tokens de API (Solo Administradores)
     */
    public function index(): JsonResponse
    {
        try {
            $tokens = PersonalAccessToken::with(['tokenable'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($token) {
                    return [
                        'id' => $token->id,
                        'name' => $token->name,
                        'abilities' => $token->abilities,
                        'last_used_at' => $token->last_used_at,
                        'created_at' => $token->created_at,
                        'tokenable_type' => $token->tokenable_type,
                        'tokenable_id' => $token->tokenable_id,
                        'user' => $token->tokenable
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $tokens
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener tokens: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear token de API para un cliente (Solo Administradores)
     */
    public function createTokenForClient(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'cliente_id' => 'required|exists:clientes,id',
                'name' => 'required|string|max:255',
                'abilities' => 'array',
                'abilities.*' => 'string'
            ]);

            $cliente = Cliente::findOrFail($validated['cliente_id']);
            
            if (!$cliente->user) {
                return response()->json([
                    'success' => false,
                    'message' => 'El cliente no tiene un usuario asociado'
                ], 400);
            }

            /** @var User $user */
            $user = $cliente->user;
            $abilities = $validated['abilities'] ?? ['*'];
            $token = $user->createToken($validated['name'], $abilities);

            return response()->json([
                'success' => true,
                'message' => 'Token creado exitosamente',
                'data' => [
                    'token' => $token->plainTextToken,
                    'token_id' => $token->accessToken->id,
                    'name' => $validated['name'],
                    'abilities' => $abilities,
                    'cliente' => $cliente
                ]
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
                'message' => 'Error al crear token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar token de API (Solo Administradores)
     */
    public function deleteToken(Cliente $cliente, $tokenId): JsonResponse
    {
        try {
            if (!$cliente->user) {
                return response()->json([
                    'success' => false,
                    'message' => 'El cliente no tiene un usuario asociado'
                ], 400);
            }

            /** @var User $user */
            $user = $cliente->user;
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

    /**
     * Obtener información del token actual del usuario autenticado
     */
    public function getCurrentTokenInfo(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $token = $request->user()->currentAccessToken();

            if ($token === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo obtener información del token actual'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'token_id' => $token->id,
                    'name' => $token->name,
                    'abilities' => $token->abilities,
                    'last_used_at' => $token->last_used_at,
                    'created_at' => $token->created_at,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'roles' => $user->roles->pluck('name')
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información del token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Revocar el token actual del usuario autenticado
     */
    public function revokeCurrentToken(Request $request): JsonResponse
    {
        try {
            $token = $request->user()->currentAccessToken();

            if ($token === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo obtener el token actual'
                ], 400);
            }

            /** @var \Laravel\Sanctum\PersonalAccessToken $currentToken */
            $currentToken = $request->user()->currentAccessToken();
            $currentToken->delete();

            return response()->json([
                'success' => true,
                'message' => 'Token revocado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al revocar token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Revocar todos los tokens del usuario autenticado
     */
    public function revokeAllTokens(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $tokenCount = $user->tokens()->count();
            
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => "Se revocaron {$tokenCount} tokens exitosamente"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al revocar tokens: ' . $e->getMessage()
            ], 500);
        }
    }
}
