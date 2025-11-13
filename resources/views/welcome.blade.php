<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'FacturaPro') }} - Sistema de Facturación</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gradient-to-br from-blue-950 via-slate-900 to-cyan-900 text-white min-h-screen">
    
    <!-- Navegación -->
    <nav class="bg-blue-800/80 backdrop-blur-sm shadow-lg fixed w-full z-50 top-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-cyan-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M7 6h10l1 8-1 4H7l-1-4 1-8z" />
                    </svg>
                    <span class="text-2xl font-bold text-cyan-300">FacturaPro</span>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-cyan-600 hover:bg-cyan-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-cyan-300 hover:text-cyan-200 font-medium transition-colors duration-200">
                            Iniciar Sesión
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-16 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239C92AC" fill-opacity="0.1"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <h1 class="text-5xl md:text-7xl font-extrabold mb-6 bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">
                    FacturaPro
                </h1>
                <p class="text-xl md:text-2xl text-cyan-200 mb-8 max-w-3xl mx-auto">
                    Sistema completo de facturación con gestión de clientes, productos y control de inventario
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-bold py-4 px-8 rounded-xl text-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                            Ir al Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-bold py-4 px-8 rounded-xl text-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                            Iniciar Sesión
                        </a>
                    @endauth
                </div>
                @guest
                <div class="mt-6 text-center">
                    <p class="text-cyan-200 text-sm bg-blue-900/50 backdrop-blur-sm rounded-lg px-4 py-2 inline-block">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        ¿Necesitas acceso? Contacta al administrador del sistema para obtener tus credenciales
                    </p>
                </div>
                @endguest
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-blue-900/50 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-cyan-300 mb-4">Características Principales</h2>
                <p class="text-xl text-cyan-200 max-w-2xl mx-auto">
                    Todo lo que necesitas para gestionar tu negocio de forma eficiente
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-blue-800/60 backdrop-blur-sm rounded-2xl p-8 hover:bg-blue-700/60 transition-all duration-300 transform hover:scale-105">
                    <div class="text-cyan-400 mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-cyan-300 mb-3">Gestión de Clientes</h3>
                    <p class="text-cyan-100">
                        Administra tu base de datos de clientes con información completa y historial de transacciones.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-blue-800/60 backdrop-blur-sm rounded-2xl p-8 hover:bg-blue-700/60 transition-all duration-300 transform hover:scale-105">
                    <div class="text-cyan-400 mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-cyan-300 mb-3">Control de Inventario</h3>
                    <p class="text-cyan-100">
                        Mantén control total de tu inventario con alertas de stock bajo y gestión automatizada.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-blue-800/60 backdrop-blur-sm rounded-2xl p-8 hover:bg-blue-700/60 transition-all duration-300 transform hover:scale-105">
                    <div class="text-cyan-400 mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-cyan-300 mb-3">Facturación Inteligente</h3>
                    <p class="text-cyan-100">
                        Genera facturas profesionales en PDF con cálculos automáticos y numeración secuencial.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Demo Credentials Section -->
    @guest
    <section class="py-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-blue-800 to-cyan-800 rounded-3xl p-12 shadow-2xl">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-cyan-300 mb-4">Prueba el Sistema</h2>
                    <p class="text-xl text-cyan-200">
                        Accede con credenciales de demostración para diferentes roles
                    </p>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Administrador -->
                    <div class="bg-blue-700/60 backdrop-blur-sm rounded-xl p-6 text-center hover:bg-blue-600/60 transition-colors duration-300">
                        <div class="text-yellow-400 mb-3">
                            <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-cyan-300 mb-2">Administrador</h3>
                        <p class="text-sm text-cyan-200 mb-3">admin@empresa.com</p>
                        <p class="text-sm text-cyan-200">admin123</p>
                    </div>

                    <!-- Usuario Demo -->
                    <div class="bg-blue-700/60 backdrop-blur-sm rounded-xl p-6 text-center hover:bg-blue-600/60 transition-colors duration-300">
                        <div class="text-green-400 mb-3">
                            <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-cyan-300 mb-2">Usuario Demo (Ventas)</h3>
                        <p class="text-sm text-cyan-200 mb-3">demo@facturacion.com</p>
                        <p class="text-sm text-cyan-200">demo123</p>
                    </div>

                    <!-- Nota -->
                    <div class="bg-blue-700/60 backdrop-blur-sm rounded-xl p-6 text-center hover:bg-blue-600/60 transition-colors duration-300">
                        <div class="text-cyan-400 mb-3">
                            <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-cyan-300 mb-2">Otros Usuarios</h3>
                        <p class="text-sm text-cyan-200 mb-3">Solo el administrador</p>
                        <p class="text-sm text-cyan-200">puede crear usuarios</p>
                    </div>
                </div>
                
                <div class="text-center mt-8">
                    <a href="{{ route('login') }}" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-bold py-3 px-8 rounded-xl text-lg transition-all duration-300 transform hover:scale-105 shadow-lg">
                        Probar Ahora
                    </a>
                </div>
            </div>
        </div>
    </section>
    @endguest

    <!-- Footer -->
    <footer class="bg-blue-950/80 backdrop-blur-sm py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-cyan-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M7 6h10l1 8-1 4H7l-1-4 1-8z" />
                    </svg>
                    <span class="text-2xl font-bold text-cyan-300">FacturaPro</span>
                </div>
                <p class="text-cyan-200 mb-6">
                    Sistema de facturación moderno y completo para tu negocio
                </p>
                <div class="border-t border-blue-800 pt-6">
                    <p class="text-cyan-300 text-sm">
                        &copy; {{ date('Y') }} FacturaPro. Desarrollado con Laravel y Tailwind CSS.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <div x-data="{ show: false }" x-show="show" x-transition class="fixed bottom-8 right-8 z-50" @scroll.window="show = window.scrollY > 500">
        <button @click="window.scrollTo({top: 0, behavior: 'smooth'})" class="bg-cyan-600 hover:bg-cyan-700 text-white p-3 rounded-full shadow-lg transition-all duration-300 transform hover:scale-110">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
            </svg>
        </button>
    </div>
</body>
</html>
