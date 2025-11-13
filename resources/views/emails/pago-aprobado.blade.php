<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Aprobado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #4CAF50;
            margin: 0;
            font-size: 28px;
        }
        .success-badge {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            font-weight: bold;
            margin: 10px 0;
        }
        .info-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #4CAF50;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .value {
            color: #333;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background-color: #e8f5e8;
            border-radius: 8px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .contact-info {
            background-color: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #2196F3;
        }
        @media (max-width: 600px) {
            .container {
                padding: 20px;
                margin: 10px;
            }
            .info-row {
                flex-direction: column;
            }
            .label, .value {
                margin: 2px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Pago Aprobado</h1>
            <div class="success-badge">APROBADO</div>
        </div>

        <p>Estimado/a <strong>{{ $cliente->nombre }}</strong>,</p>

        <p>¡Excelentes noticias! Su pago ha sido <strong>aprobado exitosamente</strong> por nuestro equipo de validación.</p>

        <div class="amount">
            Monto Aprobado: ${{ number_format($pago->monto_pagado, 2) }}
        </div>

        <div class="info-section">
            <h3 style="margin-top: 0; color: #4CAF50;">📋 Detalles del Pago</h3>
            
            <div class="info-row">
                <span class="label">Número de Transacción:</span>
                <span class="value">{{ $pago->numero_transaccion }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Tipo de Pago:</span>
                <span class="value">{{ ucfirst($pago->tipo_pago) }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Monto Pagado:</span>
                <span class="value">${{ number_format($pago->monto_pagado, 2) }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Fecha de Pago:</span>
                <span class="value">{{ $pago->created_at->format('d/m/Y H:i') }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Fecha de Aprobación:</span>
                <span class="value">{{ $pago->fecha_validacion ? $pago->fecha_validacion->format('d/m/Y H:i') : 'Recién aprobado' }}</span>
            </div>
        </div>

        <div class="info-section">
            <h3 style="margin-top: 0; color: #2196F3;">🧾 Información de la Factura</h3>
            
            <div class="info-row">
                <span class="label">Número de Factura:</span>
                <span class="value">{{ $factura->numero_factura }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Total de la Factura:</span>
                <span class="value">${{ number_format($factura->total, 2) }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Estado de la Factura:</span>
                <span class="value">
                    @if($factura->estado === 'pagada')
                        <strong style="color: #4CAF50;">✅ PAGADA COMPLETAMENTE</strong>
                    @else
                        <strong style="color: #FF9800;">⏳ Pendiente de pago completo</strong>
                    @endif
                </span>
            </div>
        </div>

        @if($factura->estado === 'pagada')
            <div style="background-color: #e8f5e8; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: center; border: 2px solid #4CAF50;">
                <h3 style="color: #4CAF50; margin: 0;">🎉 ¡Factura Completamente Pagada!</h3>
                <p style="margin: 10px 0;">Su factura ha sido pagada en su totalidad. Gracias por su pago puntual.</p>
            </div>
        @else
            @php
                $totalPagado = $factura->pagos()->where('estado', 'aprobado')->sum('monto_pagado');
                $saldoPendiente = $factura->total - $totalPagado;
            @endphp
            <div style="background-color: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; border: 2px solid #ffc107;">
                <h3 style="color: #856404; margin: 0;">💰 Saldo Pendiente</h3>
                <p style="margin: 10px 0;">
                    <strong>Saldo restante:</strong> ${{ number_format($saldoPendiente, 2) }}<br>
                    <strong>Total pagado:</strong> ${{ number_format($totalPagado, 2) }} de ${{ number_format($factura->total, 2) }}
                </p>
            </div>
        @endif

        <div class="contact-info">
            <h3 style="margin-top: 0; color: #2196F3;">📞 ¿Necesita ayuda?</h3>
            <p style="margin: 5px 0;">Si tiene alguna pregunta sobre este pago o su factura, no dude en contactarnos:</p>
            <p style="margin: 5px 0;">
                <strong>Email:</strong> soporte@facturapro.com<br>
                <strong>Teléfono:</strong> +1 (555) 123-4567<br>
                <strong>Horario:</strong> Lunes a Viernes, 9:00 AM - 6:00 PM
            </p>
        </div>

        <div class="footer">
            <p><strong>{{ config('app.name', 'FacturaPro') }}</strong></p>
            <p>Sistema de Facturación y Gestión de Pagos</p>
            <p style="font-size: 12px; color: #999;">
                Este es un correo automático, por favor no responda a esta dirección.
            </p>
        </div>
    </div>
</body>
</html>
