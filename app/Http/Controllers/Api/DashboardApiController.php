<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardApiController extends Controller
{
    /**
     * Obtener datos del dashboard según el rol del usuario
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if ($user->hasRole('Administrador')) {
                return $this->getAdminDashboard();
            } elseif ($user->hasRole('Ventas')) {
                return $this->getVentasDashboard();
            } elseif ($user->hasRole('Secretario')) {
                return $this->getSecretarioDashboard();
            } elseif ($user->hasRole('Bodega')) {
                return $this->getBodegaDashboard();
            } elseif ($user->hasRole('Cliente')) {
                return $this->getClienteDashboard($user);
            }

            return response()->json([
                'success' => false,
                'message' => 'Rol no reconocido'
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos del dashboard: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dashboard para Administradores
     */
    private function getAdminDashboard(): JsonResponse
    {
        $data = [
            'role' => 'Administrador',
            'stats' => [
                'usuarios' => [
                    'total' => User::count(),
                    'activos' => User::where('activo', true)->count(),
                    'inactivos' => User::where('activo', false)->count(),
                ],
                'clientes' => [
                    'total' => Cliente::count(),
                    'activos' => Cliente::where('activo', true)->count(),
                    'inactivos' => Cliente::where('activo', false)->count(),
                ],
                'productos' => [
                    'total' => Producto::count(),
                    'activos' => Producto::where('activo', true)->count(),
                    'bajo_stock' => Producto::where('stock', '<=', 10)->count(),
                ],
                'facturas' => [
                    'total' => Factura::count(),
                    'activas' => Factura::where('estado', 'activa')->count(),
                    'anuladas' => Factura::where('estado', 'anulada')->count(),
                    'total_facturado' => Factura::where('estado', 'activa')->sum('total'),
                ],
            ],
            'recent_activities' => [
                'facturas_recientes' => Factura::with(['cliente'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),
                'usuarios_recientes' => User::orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(['id', 'name', 'email', 'created_at']),
            ],
            'charts_data' => [
                'ventas_por_mes' => $this->getVentasPorMes(),
                'productos_mas_vendidos' => $this->getProductosMasVendidos(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Dashboard para personal de Ventas
     */
    private function getVentasDashboard(): JsonResponse
    {
        $data = [
            'role' => 'Ventas',
            'stats' => [
                'facturas' => [
                    'total' => Factura::count(),
                    'activas' => Factura::where('estado', 'activa')->count(),
                    'del_mes' => Factura::whereMonth('created_at', now()->month)->count(),
                    'total_facturado' => Factura::where('estado', 'activa')->sum('total'),
                ],
                'clientes' => [
                    'total' => Cliente::count(),
                    'activos' => Cliente::where('activo', true)->count(),
                ],
            ],
            'recent_activities' => [
                'facturas_recientes' => Factura::with(['cliente'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get(),
            ],
            'charts_data' => [
                'ventas_por_mes' => $this->getVentasPorMes(),
                'top_clientes' => $this->getTopClientes(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Dashboard para Secretarios
     */
    private function getSecretarioDashboard(): JsonResponse
    {
        $data = [
            'role' => 'Secretario',
            'stats' => [
                'clientes' => [
                    'total' => Cliente::count(),
                    'activos' => Cliente::where('activo', true)->count(),
                    'nuevos_mes' => Cliente::whereMonth('created_at', now()->month)->count(),
                ],
                'facturas' => [
                    'total' => Factura::count(),
                    'del_mes' => Factura::whereMonth('created_at', now()->month)->count(),
                ],
            ],
            'recent_activities' => [
                'clientes_recientes' => Cliente::orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get(),
                'facturas_recientes' => Factura::with(['cliente'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Dashboard para personal de Bodega
     */
    private function getBodegaDashboard(): JsonResponse
    {
        $data = [
            'role' => 'Bodega',
            'stats' => [
                'productos' => [
                    'total' => Producto::count(),
                    'activos' => Producto::where('activo', true)->count(),
                    'bajo_stock' => Producto::where('stock', '<=', 10)->count(),
                    'sin_stock' => Producto::where('stock', 0)->count(),
                ],
            ],
            'alerts' => [
                'productos_bajo_stock' => Producto::where('stock', '<=', 10)
                    ->where('stock', '>', 0)
                    ->orderBy('stock', 'asc')
                    ->limit(10)
                    ->get(['id', 'nombre', 'stock']),
                'productos_sin_stock' => Producto::where('stock', 0)
                    ->limit(10)
                    ->get(['id', 'nombre', 'stock']),
            ],
            'recent_activities' => [
                'productos_recientes' => Producto::orderBy('updated_at', 'desc')
                    ->limit(10)
                    ->get(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Dashboard para Clientes
     */
    private function getClienteDashboard(User $user): JsonResponse
    {
        $cliente = $user->cliente;
        /** @var \App\Models\Cliente $cliente */
        
        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no tiene un cliente asociado'
            ], 403);
        }

        $data = [
            'role' => 'Cliente',
            'cliente_info' => [
                'nombre' => $cliente->nombre,
                'email' => $cliente->email,
                'telefono' => $cliente->telefono,
            ],
            'stats' => [
                'facturas' => [
                    'total' => $cliente->facturas()->count(),
                    'activas' => $cliente->facturas()->where('estado', 'activa')->count(),
                    'del_mes' => $cliente->facturas()->whereMonth('created_at', now()->month)->count(),
                    'total_facturado' => $cliente->facturas()->where('estado', 'activa')->sum('total'),
                    'promedio' => $cliente->facturas()->where('estado', 'activa')->avg('total') ?: 0,
                ],
            ],
            'recent_activities' => [
                'facturas_recientes' => $cliente->facturas()
                    ->with(['productos'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get(),
            ],
            'charts_data' => [
                'compras_por_mes' => $this->getComprasPorMesCliente($cliente->id),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Obtener ventas por mes (últimos 12 meses)
     */
    private function getVentasPorMes(): array
    {
        $ventasRaw = Factura::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(*) as cantidad')
            )
            ->where('estado', 'activa')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $ventas = [];
        foreach ($ventasRaw as $item) {
            $ventas[] = [
                'periodo' => $item->year . '-' . str_pad((string)$item->month, 2, '0', STR_PAD_LEFT),
                'total' => $item->total,
                'cantidad' => $item->cantidad
            ];
        }

        return $ventas;
    }

    /**
     * Obtener productos más vendidos
     */
    private function getProductosMasVendidos(int $limit = 10): array
    {
        $productos = DB::table('factura_producto')
            ->join('productos', 'factura_producto.producto_id', '=', 'productos.id')
            ->join('facturas', 'factura_producto.factura_id', '=', 'facturas.id')
            ->where('facturas.estado', 'activa')
            ->select(
                'productos.nombre',
                DB::raw('SUM(factura_producto.cantidad) as total_vendido'),
                DB::raw('SUM(factura_producto.precio_total) as total_ingresos')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('total_vendido', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();

        return $productos;
    }

    /**
     * Obtener top clientes por ventas
     */
    private function getTopClientes(int $limit = 10): array
    {
        $clientes = Cliente::select(
                'clientes.nombre',
                DB::raw('COUNT(facturas.id) as total_facturas'),
                DB::raw('SUM(facturas.total) as total_compras')
            )
            ->join('facturas', 'clientes.id', '=', 'facturas.cliente_id')
            ->where('facturas.estado', 'activa')
            ->groupBy('clientes.id', 'clientes.nombre')
            ->orderBy('total_compras', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();

        return $clientes;
    }

    /**
     * Obtener compras por mes para un cliente específico
     */
    private function getComprasPorMesCliente(int $clienteId): array
    {
        $comprasRaw = Factura::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(*) as cantidad')
            )
            ->where('cliente_id', $clienteId)
            ->where('estado', 'activa')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $compras = [];
        foreach ($comprasRaw as $item) {
            $compras[] = [
                'periodo' => $item->year . '-' . str_pad((string)$item->month, 2, '0', STR_PAD_LEFT),
                'total' => $item->total,
                'cantidad' => $item->cantidad
            ];
        }

        return $compras;
    }
}
