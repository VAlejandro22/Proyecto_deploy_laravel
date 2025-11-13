<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PagoAprobado;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PagoController extends Controller
{
    use AuthorizesRequests;

    /**
     * Mostrar listado de pagos pendientes (para usuarios con rol Pagos)
     */
    public function index(Request $request)
    {
        Log::info('PagoController::index - Acceso iniciado', [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'user_roles' => Auth::user()->roles->pluck('name')->toArray(),
            'request_params' => $request->all()
        ]);

        $query = Pago::with(['factura.cliente', 'user'])
            ->orderBy('created_at', 'desc');

        Log::info('PagoController::index - Query base creada');

        // Filtros
        if ($request->has('estado') && $request->estado) {
            $query->where('estado', $request->estado);
            Log::info('PagoController::index - Filtro estado aplicado', ['estado' => $request->estado]);
        } else {
            // Por defecto mostrar solo pendientes
            $query->where('estado', 'pendiente');
            Log::info('PagoController::index - Filtro pendiente por defecto aplicado');
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_transaccion', 'like', "%{$search}%")
                    ->orWhereHas('factura', function ($facturaQuery) use ($search) {
                        $facturaQuery->where('numero_factura', 'like', "%{$search}%");
                    })
                    ->orWhereHas('factura.cliente', function ($clienteQuery) use ($search) {
                        $clienteQuery->where('nombre', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
            Log::info('PagoController::index - Filtro búsqueda aplicado', ['search' => $search]);
        }

        $totalPagos = $query->count();
        Log::info('PagoController::index - Total pagos encontrados', ['total' => $totalPagos]);

        $pagos = $query->paginate(15);
        
        Log::info('PagoController::index - Pagos paginados', [
            'paginated_count' => $pagos->count(),
            'total' => $pagos->total(),
            'per_page' => $pagos->perPage(),
            'current_page' => $pagos->currentPage()
        ]);

        return view('pagos.index', compact('pagos'));
    }

    /**
     * Vista de debug para diagnosticar problemas
     */
    public function debug(Request $request)
    {
        Log::info("PagoController::debug - Vista de debug accedida", [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'user_roles' => Auth::user()->roles->pluck('name')->toArray()
        ]);

        // Usar exactamente la misma lógica que index()
        $query = Pago::with(['factura.cliente', 'user']);

        // Filtro por estado si se especifica
        if ($request->has('estado') && $request->estado !== '') {
            $query->where('estado', $request->estado);
        } else {
            // Por defecto mostrar solo pendientes
            $query->where('estado', 'pendiente');
        }

        // Filtro por fecha desde
        if ($request->has('fecha_desde') && $request->fecha_desde) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        // Filtro por fecha hasta
        if ($request->has('fecha_hasta') && $request->fecha_hasta) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        // Filtro por cliente (por nombre) o búsqueda general
        if ($request->has('cliente') && $request->cliente) {
            $query->whereHas('factura.cliente', function($q) use ($request) {
                $q->where('nombre', 'LIKE', '%' . $request->cliente . '%');
            });
        }

        // Filtro de búsqueda general (search)
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('numero_transaccion', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhereHas('factura', function($subQ) use ($searchTerm) {
                      $subQ->where('numero_factura', 'LIKE', '%' . $searchTerm . '%');
                  })
                  ->orWhereHas('factura.cliente', function($subQ) use ($searchTerm) {
                      $subQ->where('nombre', 'LIKE', '%' . $searchTerm . '%');
                  });
            });
        }

        $pagos = $query->orderBy('created_at', 'desc')->paginate(15);

        Log::info("PagoController::debug - Datos preparados", [
            'total_pagos' => $pagos->total(),
            'pagos_en_pagina' => $pagos->count(),
            'pagina_actual' => $pagos->currentPage(),
            'por_pagina' => $pagos->perPage()
        ]);

        return view('pagos.debug', compact('pagos'));
    }

    /**
     * Mostrar detalles de un pago específico
     */
    public function show(Pago $pago)
    {
        $pago->load(['factura.cliente', 'user', 'validador']);
        
        return view('pagos.show', compact('pago'));
    }

    /**
     * Aprobar un pago
     */
    public function aprobar(Request $request, Pago $pago)
    {
        if ($pago->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden aprobar pagos pendientes.');
        }

        try {
            DB::beginTransaction();

            // Actualizar el pago
            $pago->update([
                'estado' => 'aprobado',
                'fecha_validacion' => now(),
                'validado_por' => Auth::id()
            ]);

            // Verificar si la factura está completamente pagada
            $factura = $pago->factura;
            /** @var \App\Models\Factura $factura */
            $totalPagado = $factura->pagos()->where('estado', 'aprobado')->sum('monto_pagado');

            if ($totalPagado >= $factura->total) {
                $factura->update(['estado' => 'pagada']);
            }

            // Cargar relaciones necesarias para el correo
            $pago->load(['factura.cliente', 'user', 'validador']);

            DB::commit();

            // Enviar correo de notificación al cliente
            try {
                $factura = $pago->factura;
                /** @var \App\Models\Factura $factura */
                $cliente = $factura->cliente;
                /** @var \App\Models\Cliente $cliente */
                if ($cliente && $cliente->email) {
                    Mail::to($cliente->email)->send(new PagoAprobado($pago));
                    Log::info("Correo de pago aprobado enviado", [
                        'pago_id' => $pago->id,
                        'cliente_email' => $cliente->email,
                        'factura_numero' => $factura->numero_factura
                    ]);
                }
            } catch (\Exception $e) {
                // Loguea el error pero no detiene la ejecución
                Log::error("No se pudo enviar el correo de pago aprobado: " . $e->getMessage(), [
                    'pago_id' => $pago->id,
                    'cliente_email' => $cliente->email ?? 'N/A',
                    'error' => $e->getMessage()
                ]);
            }

            return back()->with('success', 'Pago aprobado exitosamente. Se ha enviado una notificación al cliente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al aprobar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar un pago
     */
    public function rechazar(Request $request, Pago $pago)
    {
        $request->validate([
            'observaciones_rechazo' => 'required|string|max:1000'
        ]);

        if ($pago->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden rechazar pagos pendientes.');
        }

        try {
            DB::beginTransaction();

            // Actualizar el pago
            $pago->update([
                'estado' => 'rechazado',
                'fecha_validacion' => now(),
                'validado_por' => Auth::id(),
                'observaciones' => $pago->observaciones . "\n\nRechazado: " . $request->observaciones_rechazo
            ]);

            // La factura se mantiene pendiente
            $pago->factura->update(['estado' => 'pendiente']);

            DB::commit();

            return back()->with('success', 'Pago rechazado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al rechazar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Ver historial de pagos (todos los estados)
     */
    public function historial(Request $request)
    {
        $query = Pago::with(['factura.cliente', 'user', 'validador'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->has('estado') && $request->estado) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_transaccion', 'like', "%{$search}%")
                    ->orWhereHas('factura', function ($facturaQuery) use ($search) {
                        $facturaQuery->where('numero_factura', 'like', "%{$search}%");
                    })
                    ->orWhereHas('factura.cliente', function ($clienteQuery) use ($search) {
                        $clienteQuery->where('nombre', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('fecha_desde') && $request->fecha_desde) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta') && $request->fecha_hasta) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $pagos = $query->paginate(15);

        return view('pagos.historial', compact('pagos'));
    }
}
