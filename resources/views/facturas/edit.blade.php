@extends('layouts.app')

@section('title', 'Editar Factura')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-500 to-indigo-500 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Editar Factura</h1>
                <p class="text-purple-100">Factura #{{ $factura->numero_factura }}</p>
            </div>
            <div class="hidden md:block">
                <svg class="h-16 w-16 text-purple-200 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-4">
            <li>
                <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                </a>
            </li>
            <li class="flex items-center">
                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <a href="{{ route('facturas.index') }}" class="ml-4 text-gray-500 hover:text-gray-700 transition-colors">Facturas</a>
            </li>
            <li class="flex items-center">
                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="ml-4 text-gray-900 font-medium">Editar</span>
            </li>
        </ol>
    </nav>

    <!-- Alert if factura is not active -->
    @if($factura->estado !== 'activa')
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Factura {{ ucfirst($factura->estado) }}</h3>
                    <p class="text-sm text-yellow-700 mt-1">
                        Esta factura está en estado "{{ $factura->estado }}". Los cambios pueden estar restringidos.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Form -->
    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden">
        <form action="{{ route('facturas.update', $factura) }}" method="POST" x-data="{ loading: false }" @submit="loading = true">
            @csrf
            @method('PUT')
            
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Cliente -->
                    <div class="space-y-2">
                        <label for="cliente_id" class="block text-sm font-semibold text-gray-700">
                            <span class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Cliente *
                            </span>
                        </label>
                        <select 
                            id="cliente_id" 
                            name="cliente_id" 
                            required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white/70 backdrop-blur-sm @error('cliente_id') border-red-500 @enderror"
                        >
                            <option value="">Seleccione un cliente</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ old('cliente_id', $factura->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nombre }} - {{ $cliente->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('cliente_id')
                            <p class="text-red-500 text-sm mt-1 flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Estado -->
                    <div class="space-y-2">
                        <label for="estado" class="block text-sm font-semibold text-gray-700">
                            <span class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Estado
                            </span>
                        </label>
                        <select 
                            id="estado" 
                            name="estado" 
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-white/70 backdrop-blur-sm"
                        >
                            <option value="activa" {{ old('estado', $factura->estado) == 'activa' ? 'selected' : '' }}>Activa</option>
                            <option value="anulada" {{ old('estado', $factura->estado) == 'anulada' ? 'selected' : '' }}>Anulada</option>
                        </select>
                    </div>
                </div>

                <!-- Información de la factura -->
                <div class="mt-6 p-4 bg-blue-50/50 rounded-xl border border-blue-200">
                    <h3 class="text-sm font-semibold text-blue-800 mb-2 flex items-center">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Información de la Factura
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Número:</span>
                            <span class="font-medium text-gray-900">{{ $factura->numero_factura }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Total:</span>
                            <span class="font-medium text-green-600">${{ number_format($factura->total, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Creada:</span>
                            <span class="font-medium text-gray-900">{{ $factura->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Actualizada:</span>
                            <span class="font-medium text-gray-900">{{ $factura->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Productos de la factura -->
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="h-5 w-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Productos en la Factura
                    </h3>
                    <div class="bg-gray-50/50 rounded-xl p-4 border border-gray-200">
                        @if($factura->productos->count() > 0)
                            <div class="space-y-3">
                                @foreach($factura->productos as $producto)
                                    <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-900">{{ $producto->nombre }}</p>
                                            <p class="text-sm text-gray-500">Precio unitario: ${{ number_format($producto->pivot->precio_unitario, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-gray-900">Cantidad: {{ $producto->pivot->cantidad }}</p>
                                            <p class="text-sm font-semibold text-green-600">Subtotal: ${{ number_format($producto->pivot->subtotal, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No hay productos en esta factura</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-gray-50/80 backdrop-blur-sm px-8 py-6 border-t border-gray-200 flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-4">
                    <a 
                        href="{{ route('facturas.index') }}" 
                        class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all duration-200 font-medium"
                    >
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Cancelar
                    </a>
                    
                    <a 
                        href="{{ route('facturas.show', $factura) }}" 
                        class="inline-flex items-center px-6 py-3 border border-blue-300 text-blue-700 rounded-xl hover:bg-blue-50 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 font-medium"
                    >
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Ver Factura
                    </a>

                    <a 
                        href="{{ route('facturas.pdf', $factura) }}" 
                        class="inline-flex items-center px-6 py-3 border border-red-300 text-red-700 rounded-xl hover:bg-red-50 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200 font-medium"
                        target="_blank"
                    >
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Descargar PDF
                    </a>
                </div>

                <button 
                    type="submit" 
                    :disabled="loading"
                    :class="loading ? 'opacity-75 cursor-not-allowed' : 'hover:scale-105'"
                    class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-purple-500 to-indigo-500 text-white font-bold rounded-xl shadow-lg transition-all duration-200 focus:ring-2 focus:ring-purple-500 focus:ring-offset-2"
                >
                    <svg x-show="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg x-show="!loading" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    <span x-text="loading ? 'Actualizando...' : 'Actualizar Factura'"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
