@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Debug - Gestión de Pagos</h1>
    
    <div class="alert alert-info">
        <h3>Información de Debug:</h3>
        <p><strong>Usuario actual:</strong> {{ Auth::user()->name ?? 'No autenticado' }}</p>
        <p><strong>Roles:</strong> {{ Auth::user()->roles->pluck('name')->implode(', ') ?? 'Sin roles' }}</p>
        <p><strong>Total de pagos:</strong> {{ $pagos->total() ?? 'Variable $pagos no definida' }}</p>
        <p><strong>Pagos en página actual:</strong> {{ $pagos->count() ?? 'Variable $pagos no definida' }}</p>
    </div>

    @if(isset($pagos) && $pagos->count() > 0)
        <div class="alert alert-success">
            <h3>✅ Pagos encontrados:</h3>
            @foreach($pagos as $pago)
                <div style="border: 1px solid #ccc; margin: 10px; padding: 10px;">
                    <p><strong>ID:</strong> {{ $pago->id }}</p>
                    <p><strong>Estado:</strong> {{ $pago->estado }}</p>
                    <p><strong>Monto:</strong> ${{ number_format($pago->monto_pagado, 2) }}</p>
                    <p><strong>Tipo:</strong> {{ $pago->tipo_pago }}</p>
                    <p><strong>Número Transacción:</strong> {{ $pago->numero_transaccion }}</p>
                    <p><strong>Factura:</strong> {{ $pago->factura->numero_factura ?? 'Sin factura' }}</p>
                    <p><strong>Cliente:</strong> {{ $pago->factura->cliente->nombre ?? 'Sin cliente' }}</p>
                    <p><strong>Usuario:</strong> {{ $pago->user->name ?? 'Sin usuario' }}</p>
                    <p><strong>Fecha:</strong> {{ $pago->created_at->format('d/m/Y H:i') }}</p>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning">
            <h3>⚠️ No hay pagos o variable no definida</h3>
            <p>Verificar si la variable $pagos está llegando a la vista</p>
            @if(isset($pagos))
                <p>Variable $pagos existe pero está vacía: {{ $pagos->count() }} elementos</p>
            @else
                <p>Variable $pagos NO existe</p>
            @endif
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('pagos.index') }}" class="btn btn-primary">Volver a Vista Original</a>
    </div>
</div>
@endsection
