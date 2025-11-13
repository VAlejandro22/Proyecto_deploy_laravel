@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
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
                            <a href="{{ route('facturas.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Facturas</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Nueva Factura</span>
                        </div>
                    </li>
                </ol>
            </nav>
            
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Crear Nueva Factura</h1>
                    <p class="mt-2 text-sm text-gray-600">Genera una nueva factura de venta</p>
                </div>
                <div>
                    <a href="{{ route('facturas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Volver
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Hay algunos errores con el formulario</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('facturas.store') }}" method="POST" id="factura-form">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Form -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Client Selection -->
                    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Información del Cliente</h3>
                            <p class="mt-1 text-sm text-gray-600">Selecciona el cliente para la factura</p>
                        </div>
                        
                        <div class="p-6">
                            <div>
                                <label for="cliente_id" class="block text-sm font-medium text-gray-700">
                                    Cliente *
                                </label>
                                <div class="mt-1">
                                    <select name="cliente_id" 
                                            id="cliente_id"
                                            class="text-gray-700 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('cliente_id') border-red-500 @enderror"
                                            onchange="updateClientInfo()">
                                        <option value="">-- Seleccione un cliente --</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->id }}" 
                                                    data-email="{{ $cliente->email }}"
                                                    data-telefono="{{ $cliente->telefono }}"
                                                    {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                                {{ $cliente->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('cliente_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                
                                <!-- Cliente Info Display -->
                                <div id="cliente-info" class="mt-4 p-3 bg-blue-50 rounded-md hidden">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-blue-800" id="cliente-nombre"></p>
                                            <p class="text-sm text-blue-600" id="cliente-contacto"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Selection -->
                    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Selección de Productos</h3>
                            <p class="mt-1 text-sm text-gray-600">Agrega productos a la factura</p>
                        </div>
                        
                        <div class="p-6">
                            @if($productos->count() > 0)
                                <div class="space-y-4">
                                    @foreach($productos as $producto)
                                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                            <div class="flex-1">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-black">{{ strtoupper(substr($producto->nombre, 0, 2)) }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4 flex-1">
                                                        <h4 class="text-sm font-medium text-gray-900">{{ $producto->nombre }}</h4>
                                                        <div class="flex items-center space-x-4 mt-1">
                                                            <span class="text-sm text-gray-500">Stock: 
                                                                <span class="font-medium {{ $producto->stock > 0 ? 'text-green-600' : 'text-red-600' }}">{{ $producto->stock }}</span>
                                                            </span>
                                                            <span class="text-sm text-gray-500">Precio: 
                                                                <span class="font-medium text-blue-600">${{ number_format($producto->precio, 2) }}</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                @if($producto->stock > 0)
                                                    <div class="flex items-center space-x-2">
                                                        <label for="producto_{{ $producto->id }}" class="text-sm font-medium text-gray-700">Cantidad:</label>
                                                        <input type="number" 
                                                               name="productos[{{ $producto->id }}]" 
                                                               id="producto_{{ $producto->id }}"
                                                               min="0" 
                                                               max="{{ $producto->stock }}" 
                                                               value="{{ old('productos.'.$producto->id, 0) }}" 
                                                               class="text-gray-700 w-20 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                               onchange="calculateTotal()"
                                                               data-precio="{{ $producto->precio }}">
                                                    </div>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        Agotado
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('productos')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay productos disponibles</h3>
                                    <p class="mt-1 text-sm text-gray-500">Agrega productos al inventario para poder facturar.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('productos.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-black bg-blue-600 hover:bg-blue-700">
                                            Agregar Producto
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Invoice Summary -->
                    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Resumen de Factura</h3>
                        </div>
                        
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500">Subtotal:</span>
                                    <span class="text-sm font-medium text-gray-900" id="subtotal">$0.00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-500">IVA (19%):</span>
                                    <span class="text-sm font-medium text-gray-900" id="iva">$0.00</span>
                                </div>
                                <div class="border-t border-gray-200 pt-4">
                                    <div class="flex justify-between">
                                        <span class="text-base font-medium text-gray-900">Total:</span>
                                        <span class="text-xl font-bold text-blue-600" id="total">$0.00</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6" id="productos-seleccionados">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Productos Seleccionados:</h4>
                                <div id="lista-productos" class="space-y-2 text-sm text-gray-600">
                                    <p class="text-center text-gray-400">Ningún producto seleccionado</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Information Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Información</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Selecciona un cliente y productos</li>
                                        <li>Solo puedes vender productos con stock</li>
                                        <li>La factura se genera automáticamente</li>
                                        <li>El stock se descontará al crear la factura</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                        <div class="p-6">
                            <button type="submit" 
                                    id="submit-btn"
                                    disabled
                                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-black bg-gray-400 cursor-not-allowed disabled:opacity-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Generar Factura
                            </button>
                            
                            <div class="mt-3">
                                <a href="{{ route('facturas.index') }}" 
                                   class=" w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md  bg-red-500 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let productos = @json($productos);

function updateClientInfo() {
    const select = document.getElementById('cliente_id');
    const selectedOption = select.options[select.selectedIndex];
    const infoDiv = document.getElementById('cliente-info');
    
    if (select.value) {
        const email = selectedOption.getAttribute('data-email') || 'Sin email';
        const telefono = selectedOption.getAttribute('data-telefono') || 'Sin teléfono';
        
        document.getElementById('cliente-nombre').textContent = selectedOption.text;
        document.getElementById('cliente-contacto').textContent = `${email} - ${telefono}`;
        infoDiv.classList.remove('hidden');
    } else {
        infoDiv.classList.add('hidden');
    }
    
    validateForm();
}

function calculateTotal() {
    let subtotal = 0;
    let hasProducts = false;
    let productList = [];
    
    productos.forEach(producto => {
        const input = document.getElementById(`producto_${producto.id}`);
        if (input) {
            const cantidad = parseInt(input.value) || 0;
            if (cantidad > 0) {
                const precio = parseFloat(input.getAttribute('data-precio'));
                const total = cantidad * precio;
                subtotal += total;
                hasProducts = true;
                productList.push({
                    nombre: producto.nombre,
                    cantidad: cantidad,
                    precio: precio,
                    total: total
                });
            }
        }
    });
    
    const iva = subtotal * 0.19;
    const total = subtotal + iva;
    
    // Update summary
    document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('iva').textContent = `$${iva.toFixed(2)}`;
    document.getElementById('total').textContent = `$${total.toFixed(2)}`;
    
    // Update product list
    const listaProductos = document.getElementById('lista-productos');
    if (productList.length > 0) {
        listaProductos.innerHTML = productList.map(item => 
            `<div class="flex justify-between">
                <span>${item.cantidad}x ${item.nombre}</span>
                <span>$${item.total.toFixed(2)}</span>
            </div>`
        ).join('');
    } else {
        listaProductos.innerHTML = '<p class="text-center text-gray-400">Ningún producto seleccionado</p>';
    }
    
    validateForm();
}

function validateForm() {
    const clienteSelected = document.getElementById('cliente_id').value;
    const hasProducts = Array.from(document.querySelectorAll('input[name^="productos"]'))
        .some(input => parseInt(input.value) > 0);
    
    const submitBtn = document.getElementById('submit-btn');
    
    if (clienteSelected && hasProducts) {
        submitBtn.disabled = false;
        submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
        submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
    } else {
        submitBtn.disabled = true;
        submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
        submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateClientInfo();
    calculateTotal();
    
    // Add event listeners to all product inputs
    productos.forEach(producto => {
        const input = document.getElementById(`producto_${producto.id}`);
        if (input) {
            input.addEventListener('change', function() {
                const max = parseInt(this.getAttribute('max'));
                const value = parseInt(this.value);
                
                if (value > max) {
                    this.value = max;
                    alert(`Solo hay ${max} unidades disponibles de ${producto.nombre}`);
                }
                
                calculateTotal();
            });
        }
    });
});
</script>
@endsection
