@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Configurar Perfil</span>
                        </div>
                    </li>
                </ol>
            </nav>
            
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Configuración del Perfil</h1>
                    <p class="mt-2 text-sm text-gray-600">Administra tu información personal y configuraciones de cuenta</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Profile Information -->
                <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Información Personal</h3>
                        <p class="mt-1 text-sm text-gray-600">Actualiza tu información de perfil y dirección de correo electrónico</p>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- Change Password -->
                <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Cambiar Contraseña</h3>
                        <p class="mt-1 text-sm text-gray-600">Asegúrate de que tu cuenta use una contraseña larga y aleatoria para mantenerte seguro</p>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Account Preferences -->
                <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Preferencias de Cuenta</h3>
                        <p class="mt-1 text-sm text-gray-600">Configura tus preferencias del sistema</p>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="#" class="space-y-6">
                            @csrf
                            @method('patch')
                            
                            <!-- Email Notifications -->
                            <div>
                                <fieldset>
                                    <legend class="text-base font-medium text-gray-900">Notificaciones por Email</legend>
                                    <p class="text-sm text-gray-500">Selecciona qué notificaciones deseas recibir</p>
                                    <div class="mt-4 space-y-4">
                                        <div class="flex items-center">
                                            <input id="notify-invoices" name="notifications[]" type="checkbox" value="invoices" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" checked>
                                            <label for="notify-invoices" class="ml-3 block text-sm font-medium text-gray-700">
                                                Facturas creadas o actualizadas
                                            </label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="notify-stock" name="notifications[]" type="checkbox" value="stock" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" checked>
                                            <label for="notify-stock" class="ml-3 block text-sm font-medium text-gray-700">
                                                Alertas de stock bajo
                                            </label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="notify-reports" name="notifications[]" type="checkbox" value="reports" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="notify-reports" class="ml-3 block text-sm font-medium text-gray-700">
                                                Reportes semanales
                                            </label>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>

                            <!-- Language Preference -->
                            <div>
                                <label for="language" class="block text-sm font-medium text-gray-700">Idioma</label>
                                <select id="language" name="language" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="es" selected>Español</option>
                                    <option value="en">English</option>
                                </select>
                            </div>

                            <!-- Timezone -->
                            <div>
                                <label for="timezone" class="block text-sm font-medium text-gray-700">Zona Horaria</label>
                                <select id="timezone" name="timezone" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="America/Bogota" selected>América/Bogotá (GMT-5)</option>
                                    <option value="America/Mexico_City">América/Ciudad_de_México (GMT-6)</option>
                                    <option value="America/New_York">América/Nueva_York (GMT-5)</option>
                                </select>
                            </div>

                            <div class="flex items-center justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Guardar Preferencias
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Dashboard Customization -->
                <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Personalización del Dashboard</h3>
                        <p class="mt-1 text-sm text-gray-600">Configura cómo se muestra tu dashboard</p>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="#" class="space-y-6">
                            @csrf
                            @method('patch')
                            
                            <!-- Widget Preferences -->
                            <div>
                                <fieldset>
                                    <legend class="text-base font-medium text-gray-900">Widgets del Dashboard</legend>
                                    <p class="text-sm text-gray-500">Selecciona qué información mostrar en tu dashboard</p>
                                    <div class="mt-4 space-y-4">
                                        <div class="flex items-center">
                                            <input id="widget-sales" name="widgets[]" type="checkbox" value="sales" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" checked>
                                            <label for="widget-sales" class="ml-3 block text-sm font-medium text-gray-700">
                                                Estadísticas de Ventas
                                            </label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="widget-inventory" name="widgets[]" type="checkbox" value="inventory" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" checked>
                                            <label for="widget-inventory" class="ml-3 block text-sm font-medium text-gray-700">
                                                Estado del Inventario
                                            </label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="widget-clients" name="widgets[]" type="checkbox" value="clients" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" checked>
                                            <label for="widget-clients" class="ml-3 block text-sm font-medium text-gray-700">
                                                Resumen de Clientes
                                            </label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="widget-charts" name="widgets[]" type="checkbox" value="charts" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="widget-charts" class="ml-3 block text-sm font-medium text-gray-700">
                                                Gráficos y Métricas
                                            </label>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>

                            <!-- Date Range Preference -->
                            <div>
                                <label for="default_date_range" class="block text-sm font-medium text-gray-700">Rango de fechas por defecto</label>
                                <select id="default_date_range" name="default_date_range" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="today" selected>Hoy</option>
                                    <option value="this_week">Esta semana</option>
                                    <option value="this_month">Este mes</option>
                                    <option value="last_30_days">Últimos 30 días</option>
                                    <option value="this_year">Este año</option>
                                </select>
                            </div>

                            <!-- Theme Preference -->
                            <div>
                                <label for="theme" class="block text-sm font-medium text-gray-700">Tema del sistema</label>
                                <select id="theme" name="theme" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="light" selected>Claro</option>
                                    <option value="dark">Oscuro</option>
                                    <option value="auto">Automático (Sistema)</option>
                                </select>
                            </div>

                            <div class="flex items-center justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Guardar Configuración
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Configuración de Seguridad</h3>
                        <p class="mt-1 text-sm text-gray-600">Administra la seguridad de tu cuenta</p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            <!-- Two Factor Authentication -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">Autenticación de dos factores</h4>
                                    <p class="text-sm text-gray-500">Añade una capa extra de seguridad a tu cuenta</p>
                                </div>
                                <label for="two-factor" class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="two-factor" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Session Management -->
                            <div class="border-t border-gray-200 pt-6">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Sesiones activas</h4>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Sesión actual</p>
                                            <p class="text-sm text-gray-500">{{ request()->ip() }} • {{ request()->userAgent() }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Activa
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button type="button" class="text-sm text-red-600 hover:text-red-500">
                                        Cerrar todas las demás sesiones
                                    </button>
                                </div>
                            </div>

                            <!-- Login History -->
                            <div class="border-t border-gray-200 pt-6">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Historial de accesos recientes</h4>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-900">{{ now()->format('d/m/Y H:i') }}</span>
                                        <span class="text-gray-500">{{ request()->ip() }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-900">{{ now()->subHours(2)->format('d/m/Y H:i') }}</span>
                                        <span class="text-gray-500">192.168.1.100</span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-900">{{ now()->subDay()->format('d/m/Y H:i') }}</span>
                                        <span class="text-gray-500">192.168.1.100</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Account -->
                <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                        <h3 class="text-lg font-medium text-red-900">Zona de Peligro</h3>
                        <p class="mt-1 text-sm text-red-600">Acciones irreversibles para tu cuenta</p>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- User Summary -->
                <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Resumen de Usuario</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-16 w-16">
                                <div class="h-16 w-16 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                    <span class="text-xl font-bold text-white">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-lg font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
                                @if(Auth::user()->getRoleNames()->isNotEmpty())
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ Auth::user()->getRoleNames()->first() }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-6 border-t border-gray-200 pt-6">
                            <dl class="space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Miembro desde</dt>
                                    <dd class="text-sm text-gray-900">{{ Auth::user()->created_at->format('M Y') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Estado</dt>
                                    <dd>
                                        @if(Auth::user()->is_active ?? true)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Activo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Inactivo
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                @if(Auth::user()->facturas)
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">Facturas creadas</dt>
                                        <dd class="text-sm text-gray-900">{{ Auth::user()->facturas->count() }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Acciones Rápidas</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <a href="{{ route('dashboard') }}" class="block w-full text-left px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                                    </svg>
                                    Ir al Dashboard
                                </div>
                            </a>
                            
                            @if(auth()->user()->can('gestionar-facturas') || auth()->user()->hasRole('Administrador'))
                            <a href="{{ route('facturas.create') }}" class="block w-full text-left px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Nueva Factura
                                </div>
                            </a>
                            @endif
                            
                            @if(auth()->user()->can('gestionar-clientes') || auth()->user()->hasRole('Administrador'))
                            <a href="{{ route('clientes.create') }}" class="block w-full text-left px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Nuevo Cliente
                                </div>
                            </a>
                            @endif
                            
                            @if(auth()->user()->can('gestionar-productos') || auth()->user()->hasRole('Administrador'))
                            <a href="{{ route('productos.create') }}" class="block w-full text-left px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    Nuevo Producto
                                </div>
                            </a>
                            @endif
                            
                            <div class="border-t border-gray-200 pt-3 mt-3">
                                <a href="#" class="block w-full text-left px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Cerrar Sesión
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Info -->
                <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Información del Sistema</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Versión del Sistema</dt>
                                <dd class="text-sm text-gray-900">v2.1.0</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Última Actualización</dt>
                                <dd class="text-sm text-gray-900">Julio 2025</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Zona Horaria del Servidor</dt>
                                <dd class="text-sm text-gray-900">{{ config('app.timezone') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Entorno</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ config('app.env') === 'production' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst(config('app.env')) }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                        
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="text-center">
                                <p class="text-xs text-gray-500">
                                    © 2025 Sistema de Facturación<br>
                                    Todos los derechos reservados
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Acciones Rápidas</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <a href="{{ route('dashboard') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2V7zm0 0V5a2 2 0 012-2h6l2 2h6a2 2 0 012 2v2M7 13h10v-2H7v2z"></path>
                                </svg>
                                Ir al Dashboard
                            </a>
                            
                            @can('create', App\Models\Factura::class)
                                <a href="{{ route('facturas.create') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Nueva Factura
                                </a>
                            @endcan
                            
                            @can('viewAny', App\Models\Cliente::class)
                                <a href="{{ route('clientes.index') }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Gestionar Clientes
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

                <!-- Activity Log -->
                <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Actividad Reciente</h3>
                    </div>
                    <div class="p-6">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <li>
                                    <div class="relative pb-8">
                                        <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <div class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <div class="text-sm">
                                                        <span class="font-medium text-gray-900">Sesión iniciada</span>
                                                    </div>
                                                    <p class="mt-0.5 text-sm text-gray-500">
                                                        Acceso exitoso al sistema
                                                    </p>
                                                </div>
                                                <div class="mt-2 text-sm text-gray-700">
                                                    <p>{{ now()->format('d/m/Y H:i') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex items-start space-x-3">
                                            <div class="relative">
                                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div>
                                                    <div class="text-sm">
                                                        <span class="font-medium text-gray-900">Perfil actualizado</span>
                                                    </div>
                                                    <p class="mt-0.5 text-sm text-gray-500">
                                                        Información personal actualizada
                                                    </p>
                                                </div>
                                                <div class="mt-2 text-sm text-gray-700">
                                                    <p>{{ Auth::user()->updated_at->format('d/m/Y H:i') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
