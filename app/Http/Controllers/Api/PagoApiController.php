<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PagoApiController extends Controller
{
    /**
     * Crear un nuevo pago (para usuarios con rol Cliente)
     */
    public function store(Request $request)
    {
        // Validar que el usuario sea cliente
        if (!Auth::user()->hasRole('Cliente')) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permisos para realizar pagos'
            ], 403);
        }

        $validated = $request->validate([
            'factura_id' => 'required|exists:facturas,id',
            'tipo_pago' => ['required', Rule::in(['efectivo', 'tarjeta', 'transferencia', 'cheque'])],
            'numero_transaccion' => 'required|string|max:255',
            'monto_pagado' => 'required|numeric|min:0.01',
            'observaciones' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            // Verificar que la factura esté en estado pendiente o activa
            $factura = Factura::findOrFail($validated['factura_id']);
            
            if (!in_array($factura->estado, ['pendiente', 'activa'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'La factura no está disponible para pagos'
                ], 400);
            }

            // Verificar que el cliente de la factura corresponda al usuario autenticado
            $clienteUsuario = Auth::user()->cliente;
            /** @var \App\Models\Cliente $clienteUsuario */
            if (!$clienteUsuario || $factura->cliente_id !== $clienteUsuario->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permisos para pagar esta factura'
                ], 403);
            }

            // Verificar que el monto pagado no exceda el total de la factura
            $totalPagado = $factura->pagos()->where('estado', 'aprobado')->sum('monto_pagado');
            $montoPendiente = $factura->total - $totalPagado;

            if ($validated['monto_pagado'] > $montoPendiente) {
                return response()->json([
                    'success' => false,
                    'message' => "El monto excede lo pendiente de pago ($montoPendiente)"
                ], 400);
            }

            // Crear el pago
            $pago = Pago::create([
                'factura_id' => $validated['factura_id'],
                'user_id' => Auth::id(),
                'tipo_pago' => $validated['tipo_pago'],
                'numero_transaccion' => $validated['numero_transaccion'],
                'monto_pagado' => $validated['monto_pagado'],
                'observaciones' => $validated['observaciones'],
                'estado' => 'pendiente'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pago registrado exitosamente. Pendiente de validación.',
                'data' => [
                    'pago_id' => $pago->id,
                    'factura_numero' => $factura->numero_factura,
                    'monto_pagado' => $pago->monto_pagado,
                    'estado' => $pago->estado
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar pagos del cliente autenticado
     */
    public function index(Request $request)
    {
        // Validar que el usuario sea cliente
        if (!Auth::user()->hasRole('Cliente')) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permisos para ver pagos'
            ], 403);
        }

        $clienteUsuario = Auth::user()->cliente;
        if (!$clienteUsuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no asociado a un cliente'
            ], 400);
        }

        $query = Pago::with(['factura'])
            ->whereHas('factura', function ($q) use ($clienteUsuario) {
                /** @var \App\Models\Cliente $clienteUsuario */
                $q->where('cliente_id', $clienteUsuario->id);
            });

        // Filtros opcionales
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('factura_id')) {
            $query->where('factura_id', $request->factura_id);
        }

        $pagos = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $pagos
        ]);
    }

    /**
     * Ver detalles de un pago específico
     */
    public function show($id)
    {
        // Validar que el usuario sea cliente
        if (!Auth::user()->hasRole('Cliente')) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permisos para ver este pago'
            ], 403);
        }

        $clienteUsuario = Auth::user()->cliente;
        if (!$clienteUsuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no asociado a un cliente'
            ], 400);
        }

        $pago = Pago::with(['factura', 'validador'])
            ->whereHas('factura', function ($q) use ($clienteUsuario) {
                /** @var \App\Models\Cliente $clienteUsuario */
                $q->where('cliente_id', $clienteUsuario->id);
            })
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $pago
        ]);
    }
}
