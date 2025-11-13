@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="mb-6">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                        Dashboard - Secretario
                    </h2>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Panel de control para gestión administrativa
                    </p>
                </div>

                <!-- Estadísticas Principales -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Total Clientes -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-500 text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ $stats['total_clientes'] }}
                                </p>
                                <p class="text-blue-600 dark:text-blue-400 font-medium">
                                    Total Clientes
                                </p>
                            </div>
                        </div>
                    </div>

                    

                <!-- Accesos Rápidos -->
                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Accesos Rápidos
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @can('view_clientes')
                        <a href="{{ route('clientes.index') }}" 
                           class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="font-medium text-gray-900 dark:text-white">Gestionar Clientes</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Ver y administrar clientes</p>
                            </div>
                        </a>
                        @endcan

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
