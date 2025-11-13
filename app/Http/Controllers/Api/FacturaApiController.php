<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FacturaApiController extends Controller
{
    /**
     * Listar facturas (con diferentes permisos según el rol)
     * - Clientes: Solo sus propias facturas
     * - Administrador|Ventas: Todas las facturas
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $perPage = $request->get('per_page', 15);
            $estado = $request->get('estado');
            $fechaDesde = $request->get('fecha_desde');
            $fechaHasta = $request->get('fecha_hasta');
            $clienteId = $request->get('cliente_id');

            // Si es un cliente, solo puede ver sus propias facturas
            if ($user->hasRole('Cliente')) {
                $cliente = $user->cliente;
                /** @var \App\Models\Cliente $cliente */
                if (!$cliente) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Usuario no tiene un cliente asociado'
                    ], 403);
                }
                $query = $cliente->facturas()->with(['productos', 'cliente']);
            } else {
                // Administradores y personal de ventas pueden ver todas las facturas
                $query = Factura::with(['productos', 'cliente']);
                
                // Filtrar por cliente si se especifica
                if ($clienteId) {
                    $query->where('cliente_id', $clienteId);
                }
            }

            // Aplicar filtros comunes
            if ($estado) {
                $query->where('estado', $estado);
            }

            if ($fechaDesde) {
                $query->whereDate('created_at', '>=', $fechaDesde);
            }

            if ($fechaHasta) {
                $query->whereDate('created_at', '<=', $fechaHasta);
            }

            $facturas = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $facturas->items(),
                'pagination' => [
                    'current_page' => $facturas->currentPage(),
                    'last_page' => $facturas->lastPage(),
                    'per_page' => $facturas->perPage(),
                    'total' => $facturas->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener facturas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear una nueva factura
     * Acceso: Administrador|Ventas
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'cliente_id' => 'required|exists:clientes,id',
                'productos' => 'required|array|min:1',
                'productos.*.id' => 'required|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1',
                'productos.*.precio_unitario' => 'required|numeric|min:0.01',
                'observaciones' => 'nullable|string|max:1000'
            ]);

            DB::beginTransaction();

            // Verificar que el cliente esté activo
            $cliente = Cliente::findOrFail($validated['cliente_id']);
            if (!$cliente->activo) {
                return response()->json([
                    'success' => false,
                    'message' => 'El cliente no está activo'
                ], 422);
            }

            // Verificar stock y calcular totales
            $subtotal = 0;
            $productosValidados = [];

            foreach ($validated['productos'] as $item) {
                $producto = Producto::findOrFail($item['id']);
                
                if (!$producto->activo) {
                    return response()->json([
                        'success' => false,
                        'message' => "El producto '{$producto->nombre}' no está activo"
                    ], 422);
                }

                if ($producto->stock < $item['cantidad']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuficiente para el producto '{$producto->nombre}'. Stock disponible: {$producto->stock}"
                    ], 422);
                }

                $precioTotal = $item['cantidad'] * $item['precio_unitario'];
                $subtotal += $precioTotal;

                $productosValidados[] = [
                    'producto' => $producto,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'precio_total' => $precioTotal
                ];
            }

            // Crear la factura
            $factura = Factura::create([
                'cliente_id' => $cliente->id,
                'numero_factura' => $this->generateFacturaNumber(),
                'subtotal' => $subtotal,
                'impuestos' => $subtotal * 0.12, // 12% IVA
                'total' => $subtotal * 1.12,
                'estado' => 'activa',
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            // Asociar productos y actualizar stock
            foreach ($productosValidados as $item) {
                $factura->productos()->attach($item['producto']->id, [
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'precio_total' => $item['precio_total']
                ]);

                // Actualizar stock
                $item['producto']->decrement('stock', $item['cantidad']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Factura creada exitosamente',
                'data' => $factura->load(['productos', 'cliente'])
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->validator->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear factura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar una factura específica
     * - Clientes: Solo sus propias facturas
     * - Administrador|Ventas: Cualquier factura
     */
    public function show(Request $request, Factura $factura): JsonResponse
    {
        try {
            $user = Auth::user();

            // Si es un cliente, verificar que la factura le pertenezca
            if ($user->hasRole('Cliente')) {
                $cliente = $user->cliente;
                /** @var \App\Models\Cliente $cliente */
                if (!$cliente || $factura->cliente_id !== $cliente->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes permiso para ver esta factura'
                    ], 403);
                }
            }

            $factura->load(['productos', 'cliente']);

            return response()->json([
                'success' => true,
                'data' => $factura
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener factura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar una factura
     * Acceso: Administrador|Ventas
     */
    public function update(Request $request, Factura $factura): JsonResponse
    {
        try {
            // Solo se pueden editar facturas activas
            if ($factura->estado !== 'activa') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden editar facturas activas'
                ], 422);
            }

            $validated = $request->validate([
                'cliente_id' => 'required|exists:clientes,id',
                'productos' => 'required|array|min:1',
                'productos.*.id' => 'required|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1',
                'productos.*.precio_unitario' => 'required|numeric|min:0.01',
                'observaciones' => 'nullable|string|max:1000'
            ]);

            DB::beginTransaction();

            // Restaurar stock de productos anteriores
            foreach ($factura->productos as $producto) {
                /** @var \App\Models\Producto $producto */
                $producto->increment('stock', $producto->pivot->cantidad);
            }

            // Limpiar productos anteriores
            $factura->productos()->detach();

            // Validar y procesar nuevos productos
            $subtotal = 0;
            $productosValidados = [];

            foreach ($validated['productos'] as $item) {
                $producto = Producto::findOrFail($item['id']);
                
                if (!$producto->activo) {
                    return response()->json([
                        'success' => false,
                        'message' => "El producto '{$producto->nombre}' no está activo"
                    ], 422);
                }

                if ($producto->stock < $item['cantidad']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuficiente para el producto '{$producto->nombre}'. Stock disponible: {$producto->stock}"
                    ], 422);
                }

                $precioTotal = $item['cantidad'] * $item['precio_unitario'];
                $subtotal += $precioTotal;

                $productosValidados[] = [
                    'producto' => $producto,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'precio_total' => $precioTotal
                ];
            }

            // Actualizar factura
            $factura->update([
                'cliente_id' => $validated['cliente_id'],
                'subtotal' => $subtotal,
                'impuestos' => $subtotal * 0.12,
                'total' => $subtotal * 1.12,
                'observaciones' => $validated['observaciones'] ?? $factura->observaciones,
            ]);

            // Asociar nuevos productos y actualizar stock
            foreach ($productosValidados as $item) {
                $factura->productos()->attach($item['producto']->id, [
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'precio_total' => $item['precio_total']
                ]);

                $item['producto']->decrement('stock', $item['cantidad']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Factura actualizada exitosamente',
                'data' => $factura->load(['productos', 'cliente'])
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->validator->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar factura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Anular una factura
     * Acceso: Administrador|Ventas
     */
    public function anular(Factura $factura): JsonResponse
    {
        try {
            if ($factura->estado !== 'activa') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden anular facturas activas'
                ], 422);
            }

            DB::beginTransaction();

            // Restaurar stock de todos los productos
            foreach ($factura->productos as $producto) {
                /** @var \App\Models\Producto $producto */
                $producto->increment('stock', $producto->pivot->cantidad);
            }

            // Cambiar estado a anulada
            $factura->update(['estado' => 'anulada']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Factura anulada exitosamente',
                'data' => $factura
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al anular factura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una factura
     * Acceso: Solo Administrador
     */
    public function destroy(Factura $factura): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Si la factura está activa, restaurar stock
            if ($factura->estado === 'activa') {
                foreach ($factura->productos as $producto) {
                    /** @var \App\Models\Producto $producto */
                    $producto->increment('stock', $producto->pivot->cantidad);
                }
            }

            // Eliminar relaciones y factura
            $factura->productos()->detach();
            $factura->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Factura eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar factura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de facturas
     * - Clientes: Solo sus estadísticas
     * - Administrador|Ventas: Estadísticas generales
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if ($user->hasRole('Cliente')) {
                $cliente = $user->cliente;
                /** @var \App\Models\Cliente $cliente */
                if (!$cliente) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Usuario no tiene un cliente asociado'
                    ], 403);
                }
                $query = $cliente->facturas();
            } else {
                $query = Factura::query();
            }

            $stats = [
                'total_facturas' => $query->count(),
                'facturas_activas' => $query->where('estado', 'activa')->count(),
                'facturas_anuladas' => $query->where('estado', 'anulada')->count(),
                'total_facturado' => $query->where('estado', 'activa')->sum('total'),
                'factura_promedio' => $query->where('estado', 'activa')->avg('total') ?: 0
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar PDF de una factura
     * - Clientes: Solo sus propias facturas
     * - Administrador|Ventas: Cualquier factura
     */
    public function generatePDF(Request $request, Factura $factura): JsonResponse
    {
        try {
            $user = Auth::user();

            // Si es un cliente, verificar que la factura le pertenezca
            if ($user->hasRole('Cliente')) {
                $cliente = $user->cliente;
                /** @var \App\Models\Cliente $cliente */
                if (!$cliente || $factura->cliente_id !== $cliente->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes permiso para generar el PDF de esta factura'
                    ], 403);
                }
            }

            // Aquí implementarías la lógica para generar el PDF
            // Por ahora devolvemos la información necesaria
            $factura->load(['productos', 'cliente']);

            return response()->json([
                'success' => true,
                'message' => 'PDF generado exitosamente',
                'data' => [
                    'factura' => $factura,
                    'pdf_url' => route('facturas.pdf', $factura->id) // URL web para el PDF
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar número de factura único
     */
    private function generateFacturaNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        
        $lastFactura = Factura::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastFactura ? 
            intval(substr($lastFactura->numero_factura, -4)) + 1 : 1;
        
        return "FAC-{$year}{$month}-" . str_pad((string)$nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
