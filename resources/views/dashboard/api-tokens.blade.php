@extends('layouts.app')

@section('title', 'Gestión de API Tokens')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if (session('token_created'))
    <div class="mb-8">
        <div class="rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">
                        Token API creado exitosamente
                    </h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>El token "{{ session('token_created')['name'] }}" ha sido creado para el cliente "{{ session('token_created')['cliente'] }}". Guárdalo en un lugar seguro, no podrás verlo nuevamente:</p>
                        <div class="mt-2 relative">
                            <input type="text" readonly value="{{ session('token_created')['token'] }}" 
                                   class="w-full p-2 pr-20 text-sm bg-white border border-green-300 rounded-md focus:outline-none">
                            <button onclick="copyToClipboard('{{ session('token_created')['token'] }}')" 
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Copiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if (session('success'))
    <div class="mb-8">
        <div class="rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if (session('error'))
    <div class="mb-8">
        <div class="rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="mb-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-blue-600 px-6 py-4">
                <h3 class="text-white text-xl font-semibold flex items-center">
                    <i class="fas fa-key mr-2"></i>
                    Gestión de API Tokens para Clientes
                </h3>
            </div>
            <div class="p-6">
                <div class="mb-6">
                    <div class="rounded-md bg-blue-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Información sobre API Tokens</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Los tokens permiten a los clientes acceder a sus facturas a través de la API</li>
                                        <li>URL base de la API: <code class="bg-blue-100 px-1 rounded">{{ url('/api/cliente') }}</code></li>
                                        <li>Endpoints disponibles: <code class="bg-blue-100 px-1 rounded">/facturas</code>, <code class="bg-blue-100 px-1 rounded">/facturas/{id}</code>, <code class="bg-blue-100 px-1 rounded">/facturas-stats</code></li>
                                        <li>Usar el token en el header: <code class="bg-blue-100 px-1 rounded">Authorization: Bearer {token}</code></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('api-tokens.create') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="cliente_id" class="block text-sm font-medium text-gray-700">
                                Seleccionar Cliente
                            </label>
                            <select 
                                name="cliente_id" 
                                id="cliente_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            >
                                <option value="">Seleccione un cliente...</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}">{{ $cliente->nombre }} - {{ $cliente->email }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="nombre_token" class="block text-sm font-medium text-gray-700">
                                Nombre del Token
                            </label>
                            <input
                                type="text"
                                name="nombre_token"
                                id="nombre_token"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Ej: Token API Principal"
                                required
                            />
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <i class="fas fa-key mr-2"></i>
                            Generar Nuevo Token
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6">
        @if($clientes->count() > 0)
            @foreach($clientes as $cliente)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-50 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-full bg-blue-600 flex items-center justify-center">
                                    <i class="fas fa-user text-white text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-gray-900">{{ $cliente->nombre }}</h4>
                                <p class="text-sm text-gray-500">{{ $cliente->email }}</p>
                                @if($cliente->telefono)
                                    <p class="text-sm text-gray-500">{{ $cliente->telefono }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">
                                <span class="font-medium">{{ $cliente->facturas->count() }}</span> facturas
                            </p>
                            @if($cliente->user)
                                <p class="text-sm text-green-600">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Usuario vinculado
                                </p>
                            @else
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Sin usuario vinculado
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <h5 class="text-lg font-medium text-gray-900 mb-4">Tokens API Activos</h5>
                    @if($cliente->user && $cliente->user->tokens->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nombre del Token
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            ID
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Último uso
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($cliente->user->tokens as $token)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $token->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <code class="px-2 py-1 bg-gray-100 rounded text-sm">{{ $token->id }}</code>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $token->last_used_at ? $token->last_used_at->format('d/m/Y H:i') : 'Nunca' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 space-x-2">
                                            <form action="{{ route('api-tokens.delete', ['cliente' => $cliente->id, 'tokenId' => $token->id]) }}" 
                                                  method="POST" 
                                                  class="inline-block"
                                                  onsubmit="return confirm('¿Estás seguro de que deseas eliminar este token?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                    <i class="fas fa-trash mr-1"></i>
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="rounded-md bg-blue-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Este cliente no tiene tokens API activos.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        @else
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-users text-6xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No hay clientes registrados</h3>
                <p class="text-gray-500 mb-4">Primero debes crear clientes para poder generar tokens API.</p>
                <a href="{{ route('clientes.create') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Crear Cliente
                </a>
            </div>
        @endif
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush

@push('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            // Mostrar notificación de copiado exitoso
            const notification = document.createElement('div');
            notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg transform transition-all duration-500 ease-in-out';
            notification.textContent = '¡Token copiado al portapapeles!';
            document.body.appendChild(notification);
            
            // Remover notificación después de 2 segundos
            setTimeout(() => {
                notification.classList.add('opacity-0');
                setTimeout(() => notification.remove(), 500);
            }, 2000);
        });
    }
</script>
@endpush

@endsection
