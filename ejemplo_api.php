<?php

/**
 * Ejemplo de uso de la API de Facturas para Clientes
 * 
 * Este archivo demuestra cómo usar la API para obtener facturas de un cliente
 * usando cURL en PHP.
 */

// Configuración
$baseUrl = 'http://localhost:8000/api/cliente';
$token = 'TU_TOKEN_AQUI'; // Reemplaza con el token real generado desde el dashboard

/**
 * Función helper para hacer solicitudes a la API
 */
function makeApiRequest($url, $token, $method = 'GET', $data = null) {
    $curl = curl_init();
    
    $headers = [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);
    
    curl_close($curl);
    
    if ($error) {
        return ['error' => $error, 'http_code' => $httpCode];
    }
    
    return [
        'data' => json_decode($response, true),
        'http_code' => $httpCode
    ];
}

echo "=== API de Facturas para Clientes - Ejemplos de Uso ===\n\n";

// 1. Obtener lista de facturas
echo "1. Obteniendo lista de facturas...\n";
$response = makeApiRequest($baseUrl . '/facturas', $token);

if ($response['http_code'] === 200 && $response['data']['success']) {
    $facturas = $response['data']['data'];
    $pagination = $response['data']['pagination'];
    
    echo "✓ Facturas obtenidas exitosamente\n";
    echo "  Total de facturas: " . $pagination['total'] . "\n";
    echo "  Página actual: " . $pagination['current_page'] . "\n";
    echo "  Facturas en esta página: " . count($facturas) . "\n\n";
    
    if (!empty($facturas)) {
        echo "  Primeras facturas:\n";
        foreach (array_slice($facturas, 0, 3) as $factura) {
            echo "  - ID: {$factura['id']}, Número: {$factura['numero_factura']}, Total: \${$factura['total']}, Estado: {$factura['estado']}\n";
        }
    }
} else {
    echo "✗ Error al obtener facturas\n";
    echo "  Código HTTP: " . $response['http_code'] . "\n";
    if (isset($response['data']['message'])) {
        echo "  Mensaje: " . $response['data']['message'] . "\n";
    }
}

echo "\n" . str_repeat("-", 60) . "\n\n";

// 2. Obtener facturas con filtros
echo "2. Obteniendo facturas activas (con filtro)...\n";
$response = makeApiRequest($baseUrl . '/facturas?estado=activa&per_page=5', $token);

if ($response['http_code'] === 200 && $response['data']['success']) {
    $facturas = $response['data']['data'];
    echo "✓ Facturas activas obtenidas: " . count($facturas) . "\n";
    
    foreach ($facturas as $factura) {
        echo "  - {$factura['numero_factura']}: \${$factura['total']} ({$factura['estado']})\n";
    }
} else {
    echo "✗ Error al obtener facturas activas\n";
}

echo "\n" . str_repeat("-", 60) . "\n\n";

// 3. Obtener estadísticas del cliente
echo "3. Obteniendo estadísticas del cliente...\n";
$response = makeApiRequest($baseUrl . '/facturas-stats', $token);

if ($response['http_code'] === 200 && $response['data']['success']) {
    $stats = $response['data']['data'];
    
    echo "✓ Estadísticas obtenidas exitosamente\n";
    echo "  Total de facturas: " . $stats['total_facturas'] . "\n";
    echo "  Facturas activas: " . $stats['facturas_activas'] . "\n";
    echo "  Facturas anuladas: " . $stats['facturas_anuladas'] . "\n";
    echo "  Total facturado: $" . number_format($stats['total_facturado'], 2) . "\n";
    echo "  Promedio por factura: $" . number_format($stats['factura_promedio'], 2) . "\n";
    
    if ($stats['ultima_factura']) {
        $ultima = $stats['ultima_factura'];
        echo "  Última factura: {$ultima['numero_factura']} (\${$ultima['total']})\n";
    }
} else {
    echo "✗ Error al obtener estadísticas\n";
}

echo "\n" . str_repeat("-", 60) . "\n\n";

// 4. Obtener una factura específica (usando la primera factura si existe)
echo "4. Obteniendo una factura específica...\n";

// Primero obtenemos la lista para conseguir un ID
$listResponse = makeApiRequest($baseUrl . '/facturas?per_page=1', $token);

if ($listResponse['http_code'] === 200 && 
    $listResponse['data']['success'] && 
    !empty($listResponse['data']['data'])) {
    
    $primeraFactura = $listResponse['data']['data'][0];
    $facturaId = $primeraFactura['id'];
    
    echo "Obteniendo detalles de la factura ID: {$facturaId}...\n";
    
    $response = makeApiRequest($baseUrl . '/facturas/' . $facturaId, $token);
    
    if ($response['http_code'] === 200 && $response['data']['success']) {
        $factura = $response['data']['data'];
        
        echo "✓ Factura obtenida exitosamente\n";
        echo "  Número: {$factura['numero_factura']}\n";
        echo "  Total: \${$factura['total']}\n";
        echo "  Estado: {$factura['estado']}\n";
        echo "  Cliente: {$factura['cliente']['nombre']}\n";
        echo "  Creado por: {$factura['user']['name']}\n";
        echo "  Productos:\n";
        
        foreach ($factura['productos'] as $producto) {
            $cantidad = $producto['pivot']['cantidad'];
            $precio = $producto['pivot']['precio_unitario'];
            $subtotal = $cantidad * $precio;
            echo "    - {$producto['nombre']}: {$cantidad} x \${$precio} = \${$subtotal}\n";
        }
    } else {
        echo "✗ Error al obtener la factura específica\n";
    }
} else {
    echo "No hay facturas disponibles para mostrar detalles\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "Ejemplo completado.\n";
echo "Para usar este script:\n";
echo "1. Genera un token API desde el dashboard del administrador\n";
echo "2. Reemplaza 'TU_TOKEN_AQUI' con el token real\n";
echo "3. Ejecuta: php ejemplo_api.php\n";
echo str_repeat("=", 60) . "\n";
