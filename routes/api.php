<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FacturaApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\ClienteApiController;
use App\Http\Controllers\Api\ProductoApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\ApiTokenApiController;
use App\Http\Controllers\Api\PagoApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Middleware base para todas las rutas API
Route::middleware(['auth:sanctum', 'user.status'])->group(function () {
    
    // === RUTAS DE DASHBOARD ===
    Route::get('/dashboard', [DashboardApiController::class, 'index']);
    
    // === RUTAS DE GESTIÓN DE USUARIOS (Solo Administradores) ===
    Route::middleware('role:Administrador')->prefix('users')->group(function () {
        Route::get('/', [UserApiController::class, 'index']);
        Route::post('/', [UserApiController::class, 'store']);
        Route::get('/{user}', [UserApiController::class, 'show']);
        Route::put('/{user}', [UserApiController::class, 'update']);
        Route::delete('/{user}', [UserApiController::class, 'destroy']);
        Route::patch('/{user}/toggle-status', [UserApiController::class, 'toggleStatus']);
        Route::post('/{user}/tokens', [UserApiController::class, 'createToken']);
        Route::delete('/{user}/tokens/{tokenId}', [UserApiController::class, 'deleteToken']);
    });
    
    // === RUTAS DE GESTIÓN DE API TOKENS (Solo Administradores) ===
    Route::middleware('role:Administrador')->prefix('api-tokens')->group(function () {
        Route::get('/', [ApiTokenApiController::class, 'index']);
        Route::post('/', [ApiTokenApiController::class, 'createTokenForClient']);
        Route::delete('/{cliente}/{tokenId}', [ApiTokenApiController::class, 'deleteToken']);
    });
    
    // === RUTAS DE TOKEN DEL USUARIO ACTUAL ===
    Route::prefix('my-token')->group(function () {
        Route::get('/', [ApiTokenApiController::class, 'getCurrentTokenInfo']);
        Route::delete('/', [ApiTokenApiController::class, 'revokeCurrentToken']);
        Route::delete('/all', [ApiTokenApiController::class, 'revokeAllTokens']);
    });
    
    // === RUTAS DE CLIENTES ===
    Route::prefix('clientes')->group(function () {
        // Listar y ver clientes (Administrador|Secretario|Ventas)
        Route::get('/', [ClienteApiController::class, 'index'])
            ->middleware('role:Administrador|Secretario|Ventas');
        Route::get('/{cliente}', [ClienteApiController::class, 'show'])
            ->middleware('role:Administrador|Secretario|Ventas');
        
        // Crear y editar clientes (Administrador|Secretario)
        Route::post('/', [ClienteApiController::class, 'store'])
            ->middleware('role:Administrador|Secretario');
        Route::put('/{cliente}', [ClienteApiController::class, 'update'])
            ->middleware('role:Administrador|Secretario');
        Route::patch('/{cliente}/toggle-status', [ClienteApiController::class, 'toggleStatus'])
            ->middleware('role:Administrador|Secretario');
        
        // Eliminar clientes (Solo Administrador)
        Route::delete('/{cliente}', [ClienteApiController::class, 'destroy'])
            ->middleware('role:Administrador');
        
        // Gestión de roles de clientes (Solo Administrador)
        Route::get('/roles/available', [ClienteApiController::class, 'getRoles'])
            ->middleware('role:Administrador');
        Route::post('/{cliente}/assign-role', [ClienteApiController::class, 'assignRole'])
            ->middleware('role:Administrador');
    });
    
    // === RUTAS DE PRODUCTOS ===
    Route::prefix('productos')->group(function () {
        // Listar productos (Administrador|Bodega)
        Route::get('/', [ProductoApiController::class, 'index'])
            ->middleware('role:Administrador|Bodega');
        Route::get('/active', [ProductoApiController::class, 'getActiveProducts'])
            ->middleware('role:Administrador|Bodega|Ventas');
        Route::get('/{producto}', [ProductoApiController::class, 'show'])
            ->middleware('role:Administrador|Bodega');
        
        // Crear y editar productos (Administrador|Bodega)
        Route::post('/', [ProductoApiController::class, 'store'])
            ->middleware('role:Administrador|Bodega');
        Route::put('/{producto}', [ProductoApiController::class, 'update'])
            ->middleware('role:Administrador|Bodega');
        Route::patch('/{producto}/toggle-status', [ProductoApiController::class, 'toggleStatus'])
            ->middleware('role:Administrador|Bodega');
        Route::delete('/{producto}', [ProductoApiController::class, 'destroy'])
            ->middleware('role:Administrador|Bodega');
        
        // Verificar stock (Administrador|Bodega|Ventas)
        Route::post('/{producto}/check-stock', [ProductoApiController::class, 'checkStock'])
            ->middleware('role:Administrador|Bodega|Ventas');
    });
    
    // === RUTAS DE FACTURAS ===
    Route::prefix('facturas')->group(function () {
        // Listar facturas (permisos dinámicos según el rol)
        Route::get('/', [FacturaApiController::class, 'index']);
        Route::get('/stats', [FacturaApiController::class, 'stats']);
        Route::get('/{factura}', [FacturaApiController::class, 'show']);
        Route::get('/{factura}/pdf', [FacturaApiController::class, 'generatePDF']);
        
        // Crear y editar facturas (Administrador|Ventas)
        Route::post('/', [FacturaApiController::class, 'store'])
            ->middleware('role:Administrador|Ventas');
        Route::put('/{factura}', [FacturaApiController::class, 'update'])
            ->middleware('role:Administrador|Ventas');
        Route::patch('/{factura}/anular', [FacturaApiController::class, 'anular'])
            ->middleware('role:Administrador|Ventas');
        
        // Eliminar facturas (Solo Administrador)
        Route::delete('/{factura}', [FacturaApiController::class, 'destroy'])
            ->middleware('role:Administrador');
    });
    
    // === RUTAS ESPECÍFICAS PARA CLIENTES (Compatibilidad con el sistema existente) ===
    Route::middleware('role:Cliente')->prefix('cliente')->group(function () {
        Route::get('/facturas', [FacturaApiController::class, 'index']);
        Route::get('/facturas/{factura}', [FacturaApiController::class, 'show']);
        Route::get('/facturas-stats', [FacturaApiController::class, 'stats']);
        
        // === RUTAS DE PAGOS PARA CLIENTES ===
        Route::get('/pagos', [PagoApiController::class, 'index']);
        Route::post('/pagos', [PagoApiController::class, 'store']);
        Route::get('/pagos/{pago}', [PagoApiController::class, 'show']);
    });
});
