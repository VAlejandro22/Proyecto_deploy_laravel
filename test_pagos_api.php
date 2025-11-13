<?php

/**
 * Script de prueba para la API de Pagos
 * 
 * Requisitos:
 * 1. Crear un usuario con rol Cliente
 * 2. Crear un token de API para ese usuario
 * 3. Crear una factura en estado pendiente
 * 4. Probar el registro de pagos
 */

require_once 'vendor/autoload.php';

// Configuración de la API
$baseUrl = 'http://localhost:8000/api';
$token = ''; // Aquí debes poner el token de un usuario con rol Cliente

if (empty($token)) {
    echo "ERROR: Debes configurar un token de API válido en la variable \$token\n";
    echo "Pasos para obtener un token:\n";
    echo "1. Crear un usuario con rol Cliente\n";
    echo "2. Generar un token de API para ese usuario\n";
    echo "3. Copiar el token y pegarlo en este script\n";
    exit(1);
}

// Función para realizar peticiones HTTP
function apiRequest($method, $url, $data = null, $token = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        $token ? "Authorization: Bearer $token" : ''
    ]);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

echo "=== PRUEBAS DE LA API DE PAGOS ===\n\n";

// 1. Probar información del token
echo "1. Verificando token...\n";
$response = apiRequest('GET', "$baseUrl/my-token", null, $token);
if ($response['code'] === 200) {
    echo "✅ Token válido\n";
    echo "Usuario: " . $response['body']['user']['name'] . "\n";
    echo "Roles: " . implode(', ', $response['body']['user']['roles']) . "\n\n";
} else {
    echo "❌ Token inválido\n";
    print_r($response);
    exit(1);
}

// 2. Listar facturas del cliente
echo "2. Listando facturas del cliente...\n";
$response = apiRequest('GET', "$baseUrl/cliente/facturas", null, $token);
if ($response['code'] === 200) {
    $facturas = $response['body']['data']['data'] ?? [];
    echo "✅ Facturas encontradas: " . count($facturas) . "\n";
    
    if (empty($facturas)) {
        echo "⚠️  No hay facturas disponibles. Crea una factura primero.\n\n";
    } else {
        foreach ($facturas as $factura) {
            echo "  - ID: {$factura['id']}, Número: {$factura['numero_factura']}, Total: \${$factura['total']}, Estado: {$factura['estado']}\n";
        }
        echo "\n";
    }
} else {
    echo "❌ Error al obtener facturas\n";
    print_r($response);
}

// 3. Intentar crear un pago (necesita una factura existente)
if (!empty($facturas)) {
    $facturaParaPago = null;
    foreach ($facturas as $factura) {
        if (in_array($factura['estado'], ['pendiente', 'activa'])) {
            $facturaParaPago = $factura;
            break;
        }
    }
    
    if ($facturaParaPago) {
        echo "3. Creando un pago para la factura {$facturaParaPago['numero_factura']}...\n";
        
        $pagoData = [
            'factura_id' => $facturaParaPago['id'],
            'tipo_pago' => 'transferencia',
            'numero_transaccion' => 'TEST-' . date('YmdHis'),
            'monto_pagado' => min(100.00, $facturaParaPago['total']), // Pagar máximo $100 o el total
            'observaciones' => 'Pago de prueba generado automáticamente'
        ];
        
        $response = apiRequest('POST', "$baseUrl/cliente/pagos", $pagoData, $token);
        if ($response['code'] === 201) {
            echo "✅ Pago creado exitosamente\n";
            echo "ID del pago: " . $response['body']['data']['pago_id'] . "\n";
            echo "Estado: " . $response['body']['data']['estado'] . "\n\n";
            
            $pagoId = $response['body']['data']['pago_id'];
        } else {
            echo "❌ Error al crear el pago\n";
            print_r($response);
            $pagoId = null;
        }
    } else {
        echo "3. ⚠️  No hay facturas disponibles para pago (deben estar en estado pendiente o activa)\n\n";
        $pagoId = null;
    }
} else {
    echo "3. ⏭️  Saltando creación de pago (no hay facturas)\n\n";
    $pagoId = null;
}

// 4. Listar pagos del cliente
echo "4. Listando pagos del cliente...\n";
$response = apiRequest('GET', "$baseUrl/cliente/pagos", null, $token);
if ($response['code'] === 200) {
    $pagos = $response['body']['data']['data'] ?? [];
    echo "✅ Pagos encontrados: " . count($pagos) . "\n";
    
    foreach ($pagos as $pago) {
        echo "  - ID: {$pago['id']}, Factura: {$pago['factura']['numero_factura']}, Monto: \${$pago['monto_pagado']}, Estado: {$pago['estado']}\n";
    }
    echo "\n";
} else {
    echo "❌ Error al obtener pagos\n";
    print_r($response);
}

// 5. Ver detalles de un pago específico (si existe)
if ($pagoId) {
    echo "5. Viendo detalles del pago $pagoId...\n";
    $response = apiRequest('GET', "$baseUrl/cliente/pagos/$pagoId", null, $token);
    if ($response['code'] === 200) {
        $pago = $response['body']['data'];
        echo "✅ Detalles del pago obtenidos\n";
        echo "  - Monto: \${$pago['monto_pagado']}\n";
        echo "  - Tipo: {$pago['tipo_pago']}\n";
        echo "  - Transacción: {$pago['numero_transaccion']}\n";
        echo "  - Estado: {$pago['estado']}\n";
        echo "  - Fecha: {$pago['created_at']}\n\n";
    } else {
        echo "❌ Error al obtener detalles del pago\n";
        print_r($response);
    }
}

// 6. Pruebas de validación (estos deberían fallar)
echo "6. Probando validaciones...\n";

// Intentar pagar una factura que no existe
echo "  6.1 Intentando pagar factura inexistente...\n";
$response = apiRequest('POST', "$baseUrl/cliente/pagos", [
    'factura_id' => 99999,
    'tipo_pago' => 'efectivo',
    'numero_transaccion' => 'TEST-INVALID',
    'monto_pagado' => 100.00
], $token);

if ($response['code'] === 422 || $response['code'] === 400) {
    echo "  ✅ Validación correcta (factura inexistente rechazada)\n";
} else {
    echo "  ❌ Validación incorrecta\n";
    print_r($response);
}

// Intentar pagar con tipo de pago inválido
if (!empty($facturas)) {
    echo "  6.2 Intentando usar tipo de pago inválido...\n";
    $response = apiRequest('POST', "$baseUrl/cliente/pagos", [
        'factura_id' => $facturas[0]['id'],
        'tipo_pago' => 'criptomoneda',
        'numero_transaccion' => 'TEST-INVALID-TYPE',
        'monto_pagado' => 50.00
    ], $token);
    
    if ($response['code'] === 422) {
        echo "  ✅ Validación correcta (tipo de pago inválido rechazado)\n";
    } else {
        echo "  ❌ Validación incorrecta\n";
        print_r($response);
    }
}

echo "\n=== PRUEBAS COMPLETADAS ===\n";
echo "Para probar la validación web, ve a: http://localhost:8000/pagos\n";
echo "Necesitarás un usuario con rol 'Pagos' para aprobar/rechazar los pagos.\n";
