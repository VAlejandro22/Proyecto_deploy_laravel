<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalles del Pago #') }}{{ $pago->id }}
            </h2>
            <a href="{{ route('pagos.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Volver a Pagos
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Información del Pago -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Información del Pago</h3>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="font-medium text-gray-700">ID del Pago:</dt>
                                    <dd class="text-gray-900">{{ $pago->id }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-700">Monto Pagado:</dt>
                                    <dd class="text-gray-900 text-lg font-bold">${{ number_format($pago->monto_pagado, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-700">Tipo de Pago:</dt>
                                    <dd class="text-gray-900">{{ ucfirst($pago->tipo_pago) }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-700">Número de Transacción:</dt>
                                    <dd class="text-gray-900">{{ $pago->numero_transaccion }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-700">Estado:</dt>
                                    <dd>
                                        @switch($pago->estado)
                                            @case('pendiente')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Pendiente
                                                </span>
                                                @break
                                            @case('aprobado')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Aprobado
                                                </span>
                                                @break
                                            @case('rechazado')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Rechazado
                                                </span>
                                                @break
                                        @endswitch
                                    </dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-700">Fecha de Pago:</dt>
                                    <dd class="text-gray-900">{{ $pago->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                @if($pago->fecha_validacion)
                                <div>
                                    <dt class="font-medium text-gray-700">Fecha de Validación:</dt>
                                    <dd class="text-gray-900">{{ $pago->fecha_validacion->format('d/m/Y H:i') }}</dd>
                                </div>
                                @endif
                                @if($pago->validador)
                                <div>
                                    <dt class="font-medium text-gray-700">Validado por:</dt>
                                    <dd class="text-gray-900">{{ $pago->validador->name }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        <!-- Información de la Factura -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Información de la Factura</h3>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="font-medium text-gray-700">Número de Factura:</dt>
                                    <dd class="text-gray-900">{{ $pago->factura->numero_factura }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-700">Cliente:</dt>
                                    <dd class="text-gray-900">{{ $pago->factura->cliente->nombre }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-700">Email del Cliente:</dt>
                                    <dd class="text-gray-900">{{ $pago->factura->cliente->email }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-700">Total de la Factura:</dt>
                                    <dd class="text-gray-900 text-lg font-bold">${{ number_format($pago->factura->total, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-700">Estado de la Factura:</dt>
                                    <dd>
                                        @switch($pago->factura->estado)
                                            @case('pendiente')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Pendiente
                                                </span>
                                                @break
                                            @case('pagada')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Pagada
                                                </span>
                                                @break
                                            @case('anulada')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Anulada
                                                </span>
                                                @break
                                            @case('activa')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    Activa
                                                </span>
                                                @break
                                        @endswitch
                                    </dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-gray-700">Fecha de Factura:</dt>
                                    <dd class="text-gray-900">{{ $pago->factura->created_at->format('d/m/Y') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    @if($pago->observaciones)
                    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Observaciones</h3>
                        <p class="text-gray-900 whitespace-pre-line">{{ $pago->observaciones }}</p>
                    </div>
                    @endif

                    <!-- Información del Usuario que realizó el pago -->
                    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Realizado por</h3>
                        <dl class="space-y-2">
                            <div>
                                <dt class="font-medium text-gray-700">Usuario:</dt>
                                <dd class="text-gray-900">{{ $pago->user->name }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-700">Email:</dt>
                                <dd class="text-gray-900">{{ $pago->user->email }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Acciones -->
                    @if($pago->estado === 'pendiente')
                    <div class="mt-6 flex space-x-4">
                        <form method="POST" action="{{ route('pagos.aprobar', $pago) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('¿Está seguro de aprobar este pago?')">
                                Aprobar Pago
                            </button>
                        </form>
                        <button onclick="openRejectModal()" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Rechazar Pago
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para rechazar pago -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-2">Rechazar Pago</h3>
                <form method="POST" action="{{ route('pagos.rechazar', $pago) }}" class="mt-4">
                    @csrf
                    @method('PATCH')
                    <div class="text-left">
                        <label for="observaciones_rechazo" class="block text-sm font-medium text-gray-700">Motivo del rechazo:</label>
                        <textarea name="observaciones_rechazo" id="observaciones_rechazo" rows="3" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"></textarea>
                    </div>
                    <div class="flex justify-center space-x-3 mt-4">
                        <button type="button" onclick="closeRejectModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Rechazar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRejectModal() {
            const modal = document.getElementById('rejectModal');
            modal.classList.remove('hidden');
        }

        function closeRejectModal() {
            const modal = document.getElementById('rejectModal');
            modal.classList.add('hidden');
            document.getElementById('observaciones_rechazo').value = '';
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });
    </script>
</x-app-layout>
