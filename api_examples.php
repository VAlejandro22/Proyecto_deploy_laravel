<?php

/**
 * Ejemplo de uso de las APIs del Sistema de Facturación
 * 
 * Este archivo contiene ejemplos de cómo usar las APIs creadas.
 * NO ejecutar en producción - solo para fines de demostración.
 */

// Configuración base
$base_url = 'http://localhost:8000/api';
$token = 'tu-token-aqui'; // Reemplazar con un token válido

/**
 * Función auxiliar para hacer peticiones HTTP
 */
function makeRequest($url, $method = 'GET', $data = null, $token = null) {
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'data' => json_decode($response, true)
    ];
}

// === EJEMPLOS DE USO ===

echo "=== SISTEMA DE FACTURACIÓN - EJEMPLOS DE API ===\n\n";

// 1. Obtener información del usuario actual
echo "1. Obteniendo información del usuario actual...\n";
$response = makeRequest($base_url . '/user', 'GET', null, $token);
print_r($response);
echo "\n";

// 2. Obtener dashboard
echo "2. Obteniendo datos del dashboard...\n";
$response = makeRequest($base_url . '/dashboard', 'GET', null, $token);
print_r($response);
echo "\n";

// 3. Listar clientes
echo "3. Listando clientes...\n";
$response = makeRequest($base_url . '/clientes', 'GET', null, $token);
print_r($response);
echo "\n";

// 4. Crear un cliente (solo si tienes permisos)
echo "4. Creando un cliente...\n";
$clienteData = [
    'nombre' => 'Cliente de Prueba API',
    'email' => 'cliente.api@ejemplo.com',
    'telefono' => '987654321',
    'direccion' => 'Calle API 123',
    'nit' => '987654321-0',
    'activo' => true
];
$response = makeRequest($base_url . '/clientes', 'POST', $clienteData, $token);
print_r($response);
echo "\n";

// 5. Listar productos activos
echo "5. Listando productos activos...\n";
$response = makeRequest($base_url . '/productos/active', 'GET', null, $token);
print_r($response);
echo "\n";

// 6. Crear un producto (solo si tienes permisos)
echo "6. Creando un producto...\n";
$productoData = [
    'nombre' => 'Producto API Test',
    'descripcion' => 'Producto creado mediante API',
    'precio' => 199.99,
    'stock' => 50,
    'categoria' => 'API Testing',
    'codigo' => 'API001',
    'activo' => true
];
$response = makeRequest($base_url . '/productos', 'POST', $productoData, $token);
print_r($response);
echo "\n";

// 7. Verificar stock de un producto
echo "7. Verificando stock de producto...\n";
$stockData = ['cantidad' => 5];
$response = makeRequest($base_url . '/productos/1/check-stock', 'POST', $stockData, $token);
print_r($response);
echo "\n";

// 8. Listar facturas
echo "8. Listando facturas...\n";
$response = makeRequest($base_url . '/facturas', 'GET', null, $token);
print_r($response);
echo "\n";

// 9. Crear una factura (solo si tienes permisos)
echo "9. Creando una factura...\n";
$facturaData = [
    'cliente_id' => 1, // Ajustar según tus datos
    'productos' => [
        [
            'id' => 1,
            'cantidad' => 2,
            'precio_unitario' => 99.99
        ],
        [
            'id' => 2,
            'cantidad' => 1,
            'precio_unitario' => 49.99
        ]
    ],
    'observaciones' => 'Factura creada mediante API de prueba'
];
$response = makeRequest($base_url . '/facturas', 'POST', $facturaData, $token);
print_r($response);
echo "\n";

// 10. Obtener estadísticas de facturas
echo "10. Obteniendo estadísticas de facturas...\n";
$response = makeRequest($base_url . '/facturas/stats', 'GET', null, $token);
print_r($response);
echo "\n";

// 11. Obtener información del token actual
echo "11. Obteniendo información del token actual...\n";
$response = makeRequest($base_url . '/my-token', 'GET', null, $token);
print_r($response);
echo "\n";

// === EJEMPLOS CON FILTROS ===

echo "=== EJEMPLOS CON FILTROS ===\n\n";

// Filtrar clientes por búsqueda
echo "Filtrando clientes por búsqueda...\n";
$response = makeRequest($base_url . '/clientes?search=prueba&per_page=5', 'GET', null, $token);
print_r($response);
echo "\n";

// Filtrar facturas por estado
echo "Filtrando facturas activas...\n";
$response = makeRequest($base_url . '/facturas?estado=activa&per_page=10', 'GET', null, $token);
print_r($response);
echo "\n";

// Filtrar facturas por fecha
echo "Filtrando facturas por fecha...\n";
$fechaDesde = date('Y-m-01'); // Primer día del mes actual
$fechaHasta = date('Y-m-d');  // Fecha actual
$response = makeRequest($base_url . "/facturas?fecha_desde={$fechaDesde}&fecha_hasta={$fechaHasta}", 'GET', null, $token);
print_r($response);
echo "\n";

echo "=== FIN DE EJEMPLOS ===\n";

/**
 * NOTAS IMPORTANTES:
 * 
 * 1. Reemplazar 'tu-token-aqui' con un token válido
 * 2. Ajustar la URL base según tu configuración
 * 3. Los IDs de clientes y productos deben existir en tu base de datos
 * 4. Algunos endpoints requieren permisos específicos
 * 5. En entorno real, manejar errores apropiadamente
 * 
 * EJEMPLOS DE TOKENS:
 * - Para crear tokens usar: php artisan tinker
 *   User::find(1)->createToken('API Token')->plainTextToken
 * 
 * POSTMAN COLLECTION:
 * Importar las rutas API en Postman para pruebas más fáciles
 */
