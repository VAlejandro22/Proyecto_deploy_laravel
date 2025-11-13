@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="text-center">
                    <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                        Bienvenido al Sistema de Facturación
                    </h2>
                    
                    <div class="max-w-2xl mx-auto">
                        <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">
                            Hola <strong>{{ auth()->user()->name }}</strong>, has iniciado sesión exitosamente.
                        </p>
                        
                        @if(auth()->user()->getRoleNames()->isEmpty())
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <div class="text-left">
                                    <h3 class="font-semibold text-yellow-800 dark:text-yellow-200">
                                        Sin rol asignado
                                    </h3>
                                    <p class="text-yellow-700 dark:text-yellow-300 mt-1">
                                        Actualmente no tienes un rol asignado en el sistema. Contacta al administrador para obtener los permisos necesarios.
                                    </p>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                            <h3 class="font-semibold text-blue-800 dark:text-blue-200 mb-3">
                                Tu rol actual:
                            </h3>
                            <div class="flex flex-wrap gap-2 justify-center">
                                @foreach(auth()->user()->getRoleNames() as $role)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200">
                                    {{ $role }}
                                </span>
                                @endforeach
                            </div>
                            <p class="text-blue-700 dark:text-blue-300 mt-3">
                                Utiliza el menú de navegación para acceder a las funciones disponibles según tu rol.
                            </p>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Información del sistema -->
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Sistema de Facturación</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Gestión completa de clientes, productos y facturación
                            </p>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Control de Acceso</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Sistema basado en roles y permisos para máxima seguridad
                            </p>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Reportes y Analytics</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Dashboards personalizados según tu rol en la organización
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <a href="{{ route('profile.edit') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Configurar Perfil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
