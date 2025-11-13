<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ProductoApiController extends Controller
{
    /**
     * Listar todos los productos
     * Acceso: Administrador|Bodega
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search');
            $status = $request->get('status');
            $categoria = $request->get('categoria');

            $query = Producto::query();

            // Aplicar filtros
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                      ->orWhere('descripcion', 'like', "%{$search}%")
                      ->orWhere('codigo', 'like', "%{$search}%");
                });
            }

            if ($status !== null) {
                $query->where('activo', $status);
            }

            if ($categoria) {
                $query->where('categoria', $categoria);
            }

            $productos = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $productos->items(),
                'pagination' => [
                    'current_page' => $productos->currentPage(),
                    'last_page' => $productos->lastPage(),
                    'per_page' => $productos->perPage(),
                    'total' => $productos->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener productos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener productos activos (para formularios de facturas)
     * Acceso: Administrador|Bodega
     */
    public function getActiveProducts(): JsonResponse
    {
        try {
            $productos = Producto::where('activo', true)
                                ->where('stock', '>', 0)
                                ->select('id', 'nombre', 'precio', 'stock', 'codigo')
                                ->get();

            return response()->json([
                'success' => true,
                'data' => $productos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener productos activos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear un nuevo producto
     * Acceso: Administrador|Bodega
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:1000',
                'precio' => 'required|numeric|min:0.01',
                'stock' => 'required|integer|min:0',
                'categoria' => 'nullable|string|max:100',
                'codigo' => 'nullable|string|max:50|unique:productos,codigo',
                'activo' => 'boolean'
            ]);

            $producto = Producto::create([
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'] ?? null,
                'precio' => $validated['precio'],
                'stock' => $validated['stock'],
                'categoria' => $validated['categoria'] ?? null,
                'codigo' => $validated['codigo'] ?? null,
                'activo' => $validated['activo'] ?? true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Producto creado exitosamente',
                'data' => $producto
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
                'message' => 'Error al crear producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un producto específico
     * Acceso: Administrador|Bodega
     */
    public function show(Producto $producto): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $producto
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un producto
     * Acceso: Administrador|Bodega
     */
    public function update(Request $request, Producto $producto): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:1000',
                'precio' => 'required|numeric|min:0.01',
                'stock' => 'required|integer|min:0',
                'categoria' => 'nullable|string|max:100',
                'codigo' => ['nullable', 'string', 'max:50', Rule::unique('productos')->ignore($producto->id)],
                'activo' => 'boolean'
            ]);

            $producto->update([
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'] ?? $producto->descripcion,
                'precio' => $validated['precio'],
                'stock' => $validated['stock'],
                'categoria' => $validated['categoria'] ?? $producto->categoria,
                'codigo' => $validated['codigo'] ?? $producto->codigo,
                'activo' => $validated['activo'] ?? $producto->activo,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado exitosamente',
                'data' => $producto
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
                'message' => 'Error al actualizar producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un producto
     * Acceso: Administrador|Bodega
     */
    public function destroy(Producto $producto): JsonResponse
    {
        try {
            // Verificar si el producto está siendo usado en facturas
            if ($producto->facturas()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el producto porque está siendo usado en facturas'
                ], 409);
            }

            $producto->delete();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado del producto
     * Acceso: Administrador|Bodega
     */
    public function toggleStatus(Producto $producto): JsonResponse
    {
        try {
            $producto->update(['activo' => !$producto->activo]);

            return response()->json([
                'success' => true,
                'message' => $producto->activo ? 'Producto activado' : 'Producto desactivado',
                'data' => $producto
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado del producto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar stock disponible de un producto
     * Acceso: Administrador|Bodega
     */
    public function checkStock(Request $request, Producto $producto): JsonResponse
    {
        try {
            $validated = $request->validate([
                'cantidad' => 'required|integer|min:1'
            ]);

            $cantidadSolicitada = $validated['cantidad'];
            $stockDisponible = $producto->stock;
            $tieneStock = $stockDisponible >= $cantidadSolicitada;

            return response()->json([
                'success' => true,
                'data' => [
                    'producto_id' => $producto->id,
                    'stock_disponible' => $stockDisponible,
                    'cantidad_solicitada' => $cantidadSolicitada,
                    'tiene_stock' => $tieneStock,
                    'faltante' => $tieneStock ? 0 : $cantidadSolicitada - $stockDisponible
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
                'message' => 'Error al verificar stock: ' . $e->getMessage()
            ], 500);
        }
    }
}
