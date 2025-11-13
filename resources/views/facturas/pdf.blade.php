<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Factura #{{ $factura->numero_factura }}</h1>
    <p><strong>Cliente:</strong> {{ $factura->cliente->nombre }}</p>
    <p><strong>Fecha:</strong> {{ $factura->created_at->format('d/m/Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factura->productos as $producto)
                <tr>
                    <td>{{ $producto->nombre }}</td>
                    <td>{{ $producto->pivot->cantidad }}</td>
                    <td>${{ number_format($producto->pivot->precio_unitario, 2) }}</td>
                    <td>${{ number_format($producto->pivot->cantidad * $producto->pivot->precio_unitario, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3 style="text-align: right; margin-top: 20px;">
        Total: ${{ number_format($factura->total, 2) }}
    </h3>
</body>
</html>
