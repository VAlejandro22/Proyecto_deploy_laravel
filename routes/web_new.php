<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard principal con control de roles
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'user.status'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile',   [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'user.status'])->group(function () {
    
    // === RUTAS DE GESTIÓN DE USUARIOS (Solo Administradores) ===
    Route::middleware('permission:gestionar-usuarios')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });
    
    // === RUTAS DE CLIENTES ===
    // Rutas de creación PRIMERO (antes de las rutas con parámetros)
    Route::middleware('permission:create_clientes')->group(function () {
        Route::get('clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
        Route::post('clientes', [ClienteController::class, 'store'])->name('clientes.store');
    });
    
    Route::middleware('permission:view_clientes')->group(function () {
        Route::get('clientes', [ClienteController::class, 'index'])->name('clientes.index');
        Route::get('clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
    });
    
    Route::middleware('permission:edit_clientes')->group(function () {
        Route::get('clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
        Route::put('clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
        Route::patch('clientes/{cliente}/toggle-status', [ClienteController::class, 'toggleStatus'])->name('clientes.toggle-status');
    });
    
    Route::middleware('permission:delete_clientes')->group(function () {
        Route::delete('clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
    });
    
    // Gestión de roles (solo administradores)
    Route::middleware('permission:manage_roles')->group(function () {
        Route::get('clientes/{cliente}/roles', [ClienteController::class, 'roles'])->name('clientes.roles');
        Route::post('clientes/{cliente}/assign-role', [ClienteController::class, 'assignRole'])->name('clientes.assign-role');
    });

    // === RUTAS DE PRODUCTOS ===
    // Rutas de creación y API PRIMERO
    Route::middleware('permission:create_productos')->group(function () {
        Route::get('productos/create', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('productos', [ProductoController::class, 'store'])->name('productos.store');
    });
    
    Route::middleware('permission:view_productos')->group(function () {
        Route::get('productos', [ProductoController::class, 'index'])->name('productos.index');
        // API para obtener productos activos
        Route::get('api/productos/active', [ProductoController::class, 'getActiveProducts'])->name('productos.api.active');
        Route::get('productos/{producto}', [ProductoController::class, 'show'])->name('productos.show');
        Route::post('api/productos/{producto}/check-stock', [ProductoController::class, 'checkStock'])->name('productos.api.check-stock');
    });
    
    Route::middleware('permission:edit_productos')->group(function () {
        Route::get('productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
        Route::patch('productos/{producto}/toggle-status', [ProductoController::class, 'toggleStatus'])->name('productos.toggle-status');
    });
    
    Route::middleware('permission:delete_productos')->group(function () {
        Route::delete('productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
    });

    // === RUTAS DE FACTURAS ===
    // Rutas de creación PRIMERO
    Route::middleware('permission:create_facturas')->group(function () {
        Route::get('facturas/create', [FacturaController::class, 'create'])->name('facturas.create');
        Route::post('facturas', [FacturaController::class, 'store'])->name('facturas.store');
    });
    
    Route::middleware('permission:view_facturas')->group(function () {
        Route::get('facturas', [FacturaController::class, 'index'])->name('facturas.index');
        Route::get('facturas/{factura}', [FacturaController::class, 'show'])->name('facturas.show');
        Route::get('facturas/{factura}/pdf', [FacturaController::class, 'generatePDF'])->name('facturas.pdf');
    });
    
    Route::middleware('permission:edit_facturas')->group(function () {
        Route::get('facturas/{factura}/edit', [FacturaController::class, 'edit'])->name('facturas.edit');
        Route::put('facturas/{factura}', [FacturaController::class, 'update'])->name('facturas.update');
    });
    
    Route::middleware('permission:anular_facturas')->group(function () {
        Route::patch('facturas/{factura}/anular', [FacturaController::class, 'anular'])->name('facturas.anular');
    });
    
    Route::middleware('permission:delete_facturas')->group(function () {
        Route::delete('facturas/{factura}', [FacturaController::class, 'destroy'])->name('facturas.destroy');
    });
});

require __DIR__.'/auth.php';
