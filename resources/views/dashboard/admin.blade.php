@extends('layouts.app')

@section('title', 'Dashboard Administrador')

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
                        Token creado exitosamente
                    </h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>El token "{{ session('token_created')['name'] }}" ha sido creado. Guárdalo en un lugar seguro, no podrás verlo nuevamente:</p>
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

    <div class="mb-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-blue-600 px-6 py-4">
                <h3 class="text-white text-xl font-semibold">Gestión de Tokens de Acceso</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('users.crearTokenAcceso') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="user" class="block text-sm font-medium text-gray-700">
                                Seleccionar Usuario
                            </label>
                            <select 
                                name="user" 
                                id="user" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            >
                                <option value="">Seleccione un usuario...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->email }}</option>
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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($users as $user)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gray-50 px-6 py-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-full bg-blue-600 flex items-center justify-center">
                            <i class="fas fa-user text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-gray-900">{{ $user->name }}</h4>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4">
                <h5 class="text-lg font-medium text-gray-900 mb-4">Tokens Activos</h5>
                @if(count($user->tokens) > 0)
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
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($user->tokens as $token)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $token->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <code class="px-2 py-1 bg-gray-100 rounded text-sm">{{ $token->id }}</code>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 space-x-2">
                                        <form action="{{ route('users.deleteToken', ['user' => $user->id, 'tokenId' => $token->id]) }}" 
                                              method="POST" 
                                              class="inline-block"
                                              onsubmit="return confirm('¿Estás seguro de que deseas eliminar este token?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center p-1.5 border border-transparent rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                                    title="Eliminar Token">
                                                <i class="fas fa-trash"></i>
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
                                    Este usuario no tiene tokens activos.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Sección de API Tokens para Clientes -->
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-purple-600 px-6 py-4">
                <h3 class="text-white text-xl font-semibold flex items-center">
                    <i class="fas fa-key mr-2"></i>
                    API Tokens para Clientes
                </h3>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <p class="text-gray-600 text-sm">
                        Gestiona los tokens de API que permiten a los clientes acceder a sus facturas mediante la API REST.
                    </p>
                </div>
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">
                            Los clientes pueden usar estos tokens para consultar sus facturas, obtener detalles específicos y ver estadísticas.
                        </p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('api-tokens.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                            <i class="fas fa-cogs mr-2"></i>
                            Gestionar Tokens
                        </a>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <div class="bg-purple-50 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-users text-purple-400 text-2xl"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Clientes con Tokens</dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ \App\Models\Cliente::whereHas('user.tokens')->count() }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-key text-green-400 text-2xl"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Tokens Activos</dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ \Laravel\Sanctum\PersonalAccessToken::whereHas('tokenable', function($q) {
                                                $q->whereHas('cliente');
                                            })->count() }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-chart-line text-blue-400 text-2xl"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Uso Reciente</dt>
                                        <dd class="text-lg font-medium text-gray-900">
                                            {{ \Laravel\Sanctum\PersonalAccessToken::whereHas('tokenable', function($q) {
                                                $q->whereHas('cliente');
                                            })->whereNotNull('last_used_at')->where('last_used_at', '>=', now()->subDays(7))->count() }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- API Documentation -->
                <div class="mt-6 bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-medium text-gray-900 mb-3">Documentación rápida de la API</h4>
                    <div class="space-y-2 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Base URL:</span> 
                            <code class="bg-white px-2 py-1 rounded text-xs">{{ url('/api/cliente') }}</code>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Endpoints:</span>
                            <ul class="mt-1 ml-4 space-y-1">
                                <li><code class="bg-white px-2 py-1 rounded text-xs">GET /facturas</code> - Listar facturas del cliente</li>
                                <li><code class="bg-white px-2 py-1 rounded text-xs">GET /facturas/{id}</code> - Obtener factura específica</li>
                                <li><code class="bg-white px-2 py-1 rounded text-xs">GET /facturas-stats</code> - Estadísticas del cliente</li>
                            </ul>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Autenticación:</span> 
                            <code class="bg-white px-2 py-1 rounded text-xs">Authorization: Bearer {token}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush

@push('scripts')
<script>
    function revealToken(button, token) {
        // Crear un modal con fondo semitransparente
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center';
        
        // Contenido del modal
        const modalContent = document.createElement('div');
        modalContent.className = 'bg-white rounded-lg shadow-xl p-6 m-4 max-w-xl w-full';
        modalContent.innerHTML = `
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Token de Acceso</h3>
                <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="space-y-4">
                <p class="text-sm text-gray-500">Copie este token. Por seguridad, no se volverá a mostrar completo.</p>
                <div class="relative">
                    <input type="text" readonly value="${token}" 
                           class="w-full p-2 pr-20 text-sm bg-gray-50 border border-gray-300 rounded-md focus:outline-none">
                    <button onclick="copyToClipboard('${token}')" 
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Copiar
                    </button>
                </div>
            </div>
        `;
        
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
        
        // Cerrar modal al hacer clic fuera
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }

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
