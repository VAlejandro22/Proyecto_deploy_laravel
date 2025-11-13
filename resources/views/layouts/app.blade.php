{{-- <!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="transition-colors duration-500 ease-in-out">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name', 'Facturación Pro') }}</title>

    @vite('resources/js/app.js')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Dark Mode Alpine Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('darkMode', {
                on: localStorage.getItem('darkMode') === 'true',
                toggle() {
                    this.on = !this.on;
                    localStorage.setItem('darkMode', this.on);
                    document.documentElement.classList.toggle('dark', this.on);
                }
            });

            if (Alpine.store('darkMode').on) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });
    </script>
</head>
<body class="bg-gradient-to-br from-blue-950 via-blue-900 to-cyan-800 text-white min-h-screen antialiased transition-all duration-500">

    <div x-data="{ open: window.innerWidth >= 768 }"
         x-init="window.addEventListener('resize', () => open = window.innerWidth >= 768)"
         class="flex">

        <!-- Sidebar -->
        <aside :class="open ? 'translate-x-0' : '-translate-x-64'"
               class="fixed z-40 inset-y-0 left-0 w-64 bg-blue-800 shadow-lg transform transition-transform duration-300 ease-in-out"
               style="will-change: transform;">
            <div class="p-6 flex flex-col h-full">

                <!-- Logo -->
                <div class="flex items-center mb-8 space-x-3">
                    <svg class="h-10 w-10 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M7 6h10l1 8-1 4H7l-1-4 1-8z" />
                    </svg>
                    <h2 class="text-2xl font-bold text-cyan-300 select-none">FacturaPro</h2>
                </div>

                <!-- Navigation -->
                <nav class="flex-grow">
                    <ul class="space-y-5">
                        @foreach([
                            ['route' => 'dashboard', 'icon' => 'M3 12l2-2...M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7', 'label' => 'Dashboard'],
                            ['route' => 'clientes.index', 'icon' => 'M5.121 17.804...M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Clientes'],
                            ['route' => 'productos.index', 'icon' => 'M20 13V7a2...M12 22V11', 'label' => 'Productos'],
                            ['route' => 'facturas.index', 'icon' => 'M9 17v-2...M5 15h14', 'label' => 'Facturas'],
                            ['route' => 'pagos.index', 'icon' => 'M12 8c-1.657...M17 20h5a2 2 0 002-2v-4h-5', 'label' => 'Pagos', 'roles' => ['Administrador', 'Pagos']],
                        ] as $item)
                        @if(!isset($item['roles']) || auth()->user()->hasAnyRole($item['roles']))
                        <li>
                            <a href="{{ route($item['route']) }}"
                               class="flex items-center px-4 py-2 rounded-md hover:bg-cyan-700 transition">
                                <svg class="h-6 w-6 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2"
                                     viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="{{ $item['icon'] }}" />
                                </svg>
                                <span class="ml-3">{{ $item['label'] }}</span>
                            </a>
                        </li>
                        @endif
                        @endforeach

                        <!-- Logout -->
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="mt-6">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 rounded-md hover:bg-red-600 transition text-red-400">
                                    Salir
                                </button>
                            </form>
                        </li>
                    </ul>
                </nav>

                <!-- Footer -->
                <div class="mt-auto pt-6 text-center text-cyan-400 text-xs select-none">
                    © {{ date('Y') }} {{ config('app.name', 'FactuPro') }}
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 min-h-screen transition-all duration-300" :class="open ? 'ml-64' : 'ml-0'">

            <!-- Top Navbar -->
            <nav class="flex items-center justify-between bg-blue-900 py-3 px-6 shadow-md">
                <button @click="open = !open" class="text-white focus:outline-none" aria-label="Toggle sidebar">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="flex items-center gap-6 ml-auto">
                    <!-- Dark Mode Toggle -->
                    <button @click="Alpine.store('darkMode').toggle()" class="focus:outline-none">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path x-show="!Alpine.store('darkMode').on" d="M10 2a8 8 0 100 16 8 8 0 000-16z" />
                            <path x-show="Alpine.store('darkMode').on" d="M17.293 13.293A8 8 0 116.707 2.707a8.003 8.003 0 0010.586 10.586z" />
                        </svg>
                    </button>

                    <!-- User Info -->
                    <div class="text-sm text-cyan-200 select-none">
                        {{ Auth::user()->name ?? 'Invitado' }}
                    </div>
                </div>
            </nav>

            <!-- Yield Content -->
            <main class="flex-grow p-6">
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html> --}}

<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="transition-colors duration-500 ease-in-out">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name', 'Facturación Pro') }}</title>

    @vite('resources/js/app.js')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('darkMode', {
                on: localStorage.getItem('darkMode') === 'true',
                toggle() {
                    this.on = !this.on;
                    localStorage.setItem('darkMode', this.on);
                    document.documentElement.classList.toggle('dark', this.on);
                }
            });

            if (Alpine.store('darkMode').on) {
                document.documentElement.classList.add('dark');
            }
        });
    </script>
</head>
<body class="bg-gradient-to-br from-blue-950 via-blue-900 to-cyan-800 text-white min-h-screen antialiased transition-all duration-500">

    <div x-data="{ open: window.innerWidth >= 768 }"
         x-init="window.addEventListener('resize', () => open = window.innerWidth >= 768)"
         class="flex">

        <!-- Sidebar -->
        <aside :class="open ? 'translate-x-0' : '-translate-x-64'"
               class="fixed z-40 inset-y-0 left-0 w-64 bg-blue-800 shadow-lg transform transition-transform duration-300 ease-in-out"
               style="will-change: transform;">
            <div class="p-6 flex flex-col h-full">

                <!-- Logo -->
                <div class="flex items-center mb-8 space-x-3">
                    <svg class="h-10 w-10 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M7 6h10l1 8-1 4H7l-1-4 1-8z" />
                    </svg>
                    <h2 class="text-2xl font-bold text-cyan-300 select-none">FacturaPro</h2>
                </div>

                <!-- Navigation -->
                <nav class="flex-grow">
                    <ul class="space-y-4">

                        <li>
                            <a href="{{ route('dashboard') }}"
                               class="flex items-center px-4 py-2 rounded-md hover:bg-cyan-700 transition">
                                <svg class="h-6 w-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6m-6 4v6" />
                                </svg>
                                <span class="ml-3">Dashboard</span>
                            </a>
                        </li>

                        @role('Administrador|Secretario|Ventas')
                        <li>
                            <a href="{{ route('clientes.index') }}"
                               class="flex items-center px-4 py-2 rounded-md hover:bg-cyan-700 transition">
                                <svg class="h-6 w-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M5.121 17.804A9.003 9.003 0 0112 3a9.003 9.003 0 016.879 14.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="ml-3">Clientes</span>
                            </a>
                        </li>
                        @endrole

                        @role('Administrador|Bodega')
                        <li>
                            <a href="{{ route('productos.index') }}"
                               class="flex items-center px-4 py-2 rounded-md hover:bg-cyan-700 transition">
                                <svg class="h-6 w-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0l-8 5-8-5" />
                                </svg>
                                <span class="ml-3">Productos</span>
                            </a>
                        </li>
                        @endrole

                        @role('Administrador|Ventas')
                        <li>
                            <a href="{{ route('facturas.index') }}"
                               class="flex items-center px-4 py-2 rounded-md hover:bg-cyan-700 transition">
                                <svg class="h-6 w-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m4 0V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10m14 0H5" />
                                </svg>
                                <span class="ml-3">Facturas</span>
                            </a>
                        </li>
                        @endrole

                        @role('Administrador|Pagos')
                        <li>
                            <a href="{{ route('pagos.index') }}"
                               class="flex items-center px-4 py-2 rounded-md hover:bg-cyan-700 transition">
                                <svg class="h-6 w-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                                <span class="ml-3">Pagos</span>
                            </a>
                        </li>
                        @endrole

                        @role('Administrador')
                        <li>
                            <a href="{{ route('users.index') }}"
                               class="flex items-center px-4 py-2 rounded-md hover:bg-cyan-700 transition">
                                <svg class="h-6 w-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="ml-3">Usuarios</span>
                            </a>
                        </li>
                        @endrole

                        <li class="mt-6">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-red-600 w-full text-left px-4 py-2 rounded-md hover:bg-red-100 hover:text-gray-400 text-white-400">
                                    Salir
                                </button>
                            </form>
                        </li>
                    </ul>
                </nav>

                <!-- Footer -->
                <div class="mt-auto pt-6 text-center text-cyan-400 text-xs select-none">
                    © {{ date('Y') }} {{ config('app.name', 'FactuPro') }}
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 min-h-screen transition-all duration-300" :class="open ? 'ml-64' : 'ml-0'">

            <!-- Top Navbar -->
            <nav class="flex items-center justify-between bg-blue-900 py-3 px-6 shadow-md">
                <button @click="open = !open" class="text-white focus:outline-none" aria-label="Toggle sidebar">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="flex items-center gap-6 ml-auto">
                    <!-- Dark Mode Toggle -->
                    <button @click="Alpine.store('darkMode').toggle()" class="focus:outline-none">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path x-show="!Alpine.store('darkMode').on" d="M10 2a8 8 0 100 16 8 8 0 000-16z" />
                            <path x-show="Alpine.store('darkMode').on" d="M17.293 13.293A8 8 0 116.707 2.707a8.003 8.003 0 0010.586 10.586z" />
                        </svg>
                    </button>

                    <!-- User Info -->
                    <div class="text-sm text-cyan-200 select-none">
                        {{ Auth::user()->name ?? 'Invitado' }}
                    </div>
                </div>
            </nav>

            <!-- Page Header (if provided) -->
            @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endisset

            <!-- Yield Content -->
            <main class="p-6">
                @yield('content')
                {{ $slot ?? '' }}
            </main>
        </div>
    </div>
</body>
</html>
