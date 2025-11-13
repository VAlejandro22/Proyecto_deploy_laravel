<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FacturaController extends Controller
{
    use AuthorizesRequests;
    // En Laravel 12, el middleware se define en las rutas, no en el constructor del controlador

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Factura::with(['cliente', 'user', 'productos']);

        // Filtro de búsqueda
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_factura', 'like', "%{$search}%")
                    ->orWhereHas('cliente', function ($clienteQuery) use ($search) {
                        $clienteQuery->where('nombre', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filtro de estado
        if ($request->has('estado') && $request->estado) {
            $query->where('estado', $request->estado);
        }

        // Filtro de fecha
        if ($request->has('fecha_desde') && $request->fecha_desde) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta') && $request->fecha_hasta) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        // Solo mostrar facturas propias si no es administrador
        if (!auth()->user()->hasRole('Administrador')) {
            $query->where('user_id', auth()->id());
        }

        $facturas = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('facturas.index', compact('facturas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::where('is_active', true)->orderBy('nombre')->get();
        $productos = Producto::where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('nombre')
            ->get();

        return view('facturas.create', compact('clientes', 'productos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $validated = $request->validate([
        //     'cliente_id' => 'required|exists:clientes,id',
        //     'productos' => 'required|array|min:1',
        //     'productos.*.id' => 'required|exists:productos,id',
        //     'productos.*.cantidad' => 'required|integer|min:1',
        // ]);

        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'productos' => 'required|array|min:1',
        ]);

        // Convertir formato a lo que espera el proceso
        $productosFormateados = [];
        foreach ($validated['productos'] as $productoId => $cantidad) {
            if ($cantidad > 0) {
                $productosFormateados[] = [
                    'id' => $productoId,
                    'cantidad' => $cantidad,
                ];
            }
        }

        // Validar manualmente cada producto
        if (count($productosFormateados) === 0) {
            return back()->withErrors(['productos' => 'Debes seleccionar al menos un producto con cantidad mayor a cero.'])->withInput();
        }

        try {
            DB::beginTransaction();

            $total = 0;
            $productosParaFactura = [];

            // Validar stock y calcular total
            // foreach ($validated['productos'] as $productoData) {
            foreach ($productosFormateados as $productoData) {
                $producto = Producto::findOrFail($productoData['id']);
                $cantidad = $productoData['cantidad'];

                // Verificar stock disponible
                if ($producto->stock < $cantidad) {
                    throw new Exception("Stock insuficiente para {$producto->nombre}. Stock disponible: {$producto->stock}");
                }

                $subtotal = $producto->precio * $cantidad;
                $total += $subtotal;

                $productosParaFactura[] = [
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $subtotal
                ];
            }

            // Crear la factura
            $factura = Factura::create([
                'user_id' => auth()->id(),
                'cliente_id' => $validated['cliente_id'],
                'total' => $total,
                'estado' => 'pendiente' // Cambiar estado por defecto a pendiente
            ]);

            // Asociar productos y descontar stock
            foreach ($productosParaFactura as $item) {
                $factura->productos()->attach($item['producto']->id, [
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario']
                ]);

                // Descontar stock
                $item['producto']->decrement('stock', $item['cantidad']);
            }

            DB::commit();

            // Recargar relaciones necesarias
            $factura->load(['cliente', 'user', 'productos']);

            // Enviar factura por correo
            try {
                /** @var \App\Models\Cliente $cliente */
                $cliente = $factura->cliente;
                $clienteEmail = $cliente->email;
                if ($clienteEmail) {
                    Mail::to($clienteEmail)->send(new \App\Mail\FacturaCreada($factura));
                }
            } catch (\Exception $e) {
                // Loguea pero no detiene la ejecución
                Log::error("No se pudo enviar la factura por email: " . $e->getMessage());
            }


            return redirect()->route('facturas.show', $factura)
                ->with('success', 'Factura creada exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la factura: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Factura $factura)
    {
        $this->authorize('view', $factura);

        $factura->load(['cliente', 'user', 'productos']);

        return view('facturas.show', compact('factura'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Factura $factura)
    {
        $this->authorize('update', $factura);

        // Solo se pueden editar facturas activas
        if ($factura->estado !== 'activa') {
            return redirect()->route('facturas.index')
                ->with('error', 'Solo se pueden editar facturas activas.');
        }

        $clientes = Cliente::where('is_active', true)->orderBy('nombre')->get();
        $productos = Producto::where('is_active', true)->orderBy('nombre')->get();

        $factura->load(['cliente', 'productos']);

        return view('facturas.edit', compact('factura', 'clientes', 'productos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Factura $factura)
    {
        $this->authorize('update', $factura);

        // Solo se pueden editar facturas activas
        if ($factura->estado !== 'activa') {
            return redirect()->route('facturas.index')
                ->with('error', 'Solo se pueden editar facturas activas.');
        }

        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Restaurar stock de productos originales
            foreach ($factura->productos as $producto) {
                /** @var \App\Models\Producto $producto */
                $cantidad = $producto->pivot->cantidad ?? 0;
                $producto->increment('stock', $cantidad);
            }

            // Eliminar productos asociados
            $factura->productos()->detach();

            $total = 0;
            $productosParaFactura = [];

            // Validar stock y calcular nuevo total
            foreach ($validated['productos'] as $productoData) {
                $producto = Producto::findOrFail($productoData['id']);
                $cantidad = $productoData['cantidad'];

                // Verificar stock disponible
                if ($producto->stock < $cantidad) {
                    throw new Exception("Stock insuficiente para {$producto->nombre}. Stock disponible: {$producto->stock}");
                }

                $subtotal = $producto->precio * $cantidad;
                $total += $subtotal;

                $productosParaFactura[] = [
                    'producto' => $producto,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $subtotal
                ];
            }

            // Actualizar la factura
            $factura->update([
                'cliente_id' => $validated['cliente_id'],
                'total' => $total
            ]);

            // Asociar nuevos productos y descontar stock
            foreach ($productosParaFactura as $item) {
                $factura->productos()->attach($item['producto']->id, [
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario']
                ]);

                // Descontar stock
                $item['producto']->decrement('stock', $item['cantidad']);
            }

            DB::commit();

            return redirect()->route('facturas.show', $factura)
                ->with('success', 'Factura actualizada exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la factura: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Factura $factura)
    {
        try {
            DB::beginTransaction();

            // Restaurar stock si la factura estaba activa
            if ($factura->estado === 'activa') {
                foreach ($factura->productos as $producto) {
                    /** @var \App\Models\Producto $producto */
                    $cantidad = $producto->pivot->cantidad ?? 0;
                    $producto->increment('stock', $cantidad);
                }
            }

            // Eliminar la factura
            $factura->delete();

            DB::commit();

            return redirect()->route('facturas.index')
                ->with('success', 'Factura eliminada exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('facturas.index')
                ->with('error', 'Error al eliminar la factura: ' . $e->getMessage());
        }
    }

    /**
     * Anular una factura
     */
    public function anular(Factura $factura)
    {
        if ($factura->estado !== 'activa') {
            return redirect()->route('facturas.index')
                ->with('error', 'Solo se pueden anular facturas activas.');
        }

        try {
            DB::beginTransaction();

            // Restaurar stock de productos
            foreach ($factura->productos as $producto) {
                /** @var \App\Models\Producto $producto */
                $cantidad = $producto->pivot->cantidad ?? 0;
                $producto->increment('stock', $cantidad);
            }

            // Cambiar estado a anulada
            $factura->update(['estado' => 'anulada']);

            DB::commit();

            return redirect()->route('facturas.show', $factura)
                ->with('success', 'Factura anulada exitosamente. El stock ha sido restaurado.');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('facturas.show', $factura)
                ->with('error', 'Error al anular la factura: ' . $e->getMessage());
        }
    }

    /**
     * Generar PDF de la factura
     */
    public function generatePDF(Factura $factura)
    {
        $factura->load(['cliente', 'user', 'productos']);

        $pdf = Pdf::loadView('facturas.pdf', compact('factura'));

        return $pdf->download("factura-{$factura->numero_factura}.pdf");
    }
}
