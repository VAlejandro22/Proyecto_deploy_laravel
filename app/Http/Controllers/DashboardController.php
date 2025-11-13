<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Factura;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Estadísticas básicas según el rol
        $stats = [];
        
        // Check if user is admin by email (fallback for role issues)
        $isAdmin = $user->hasRole('Administrador') || 
                   in_array($user->email, ['admin@empresa.com', 'admin@facturacion.com']);
        
        if ($isAdmin) {
            // Give admin role if they don't have it
            if (!$user->hasRole('Administrador')) {
                try {
                    DB::beginTransaction();
                    
                    $adminRole = \Spatie\Permission\Models\Role::where('name', 'Administrador')->first();
                    if ($adminRole) {
                        $user->assignRole($adminRole);
                    }
                    
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    // Log error but continue with dashboard
                    \Log::error('Error assigning admin role: ' . $e->getMessage());
                }
            }
            
            $stats = [
                'total_clientes' => Cliente::count(),
                'clientes_activos' => Cliente::where('is_active', true)->count(),
                'total_productos' => Producto::count(),
                'productos_activos' => Producto::where('is_active', true)->count(),
                'total_facturas' => Factura::count(),
                'facturas_activas' => Factura::where('estado', 'activa')->count(),
                'total_usuarios' => User::count(),
                'usuarios_activos' => User::where('is_active', true)->count(),
                'ventas_hoy' => Factura::whereDate('created_at', today())
                    ->where('estado', 'activa')
                    ->sum('total'),
                'ventas_mes' => Factura::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->where('estado', 'activa')
                    ->sum('total'),
                'productos_bajo_stock' => Producto::where('stock', '<', 10)
                    ->where('is_active', true)
                    ->count(),
            ];
            
            // Gráficos adicionales para admin
            $ventasDiarias = $this->getVentasDiarias();
            $topProductos = $this->getTopProductos();
            $users=User::all();
            return view('dashboard.admin', compact('stats', 'ventasDiarias', 'topProductos','users'));
            
        } elseif ($user->hasRole(['Secretario'])) {
            $stats = [
                'total_clientes' => Cliente::count(),
                'total_facturas' => Factura::count(),
                'ventas_hoy' => Factura::whereDate('created_at', today())
                    ->where('estado', 'activa')
                    ->sum('total'),
            ];
            
            return view('dashboard.secretario', compact('stats'));
            
        } elseif ($user->hasRole('Bodega')) {
            $stats = [
                'total_productos' => Producto::count(),
                'productos_activos' => Producto::where('is_active', true)->count(),
                'productos_bajo_stock' => Producto::where('stock', '<', 10)
                    ->where('is_active', true)
                    ->count(),
            ];
            
            $productosBajoStock = Producto::where('stock', '<', 10)
                ->where('is_active', true)
                ->get();
                
            return view('dashboard.bodega', compact('stats', 'productosBajoStock'));
            
        } elseif ($user->hasRole('Ventas')) {
            $stats = [
                'mis_facturas_hoy' => Factura::where('user_id', $user->id)
                    ->whereDate('created_at', today())
                    ->count(),
                'mis_ventas_hoy' => Factura::where('user_id', $user->id)
                    ->whereDate('created_at', today())
                    ->where('estado', 'activa')
                    ->sum('total'),
                'total_clientes' => Cliente::where('is_active', true)->count(),
                'productos_disponibles' => Producto::where('stock', '>', 0)
                    ->where('is_active', true)
                    ->count(),
            ];
            
            return view('dashboard.ventas', compact('stats'));
        }
        
        // Dashboard por defecto
        return view('dashboard.default');
    }
    
    private function getVentasDiarias()
    {
        return Factura::selectRaw('DATE(created_at) as fecha, SUM(total) as total')
            ->where('estado', 'activa')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();
    }
    
    private function getTopProductos()
    {
        return Producto::select('productos.*')
            ->join('factura_producto', 'productos.id', '=', 'factura_producto.producto_id')
            ->join('facturas', 'factura_producto.factura_id', '=', 'facturas.id')
            ->where('facturas.estado', 'activa')
            ->where('facturas.created_at', '>=', now()->subDays(30))
            ->selectRaw('productos.*, SUM(factura_producto.cantidad) as total_vendido')
            ->groupBy('productos.id')
            ->orderBy('total_vendido', 'desc')
            ->limit(5)
            ->get();
    }
}
