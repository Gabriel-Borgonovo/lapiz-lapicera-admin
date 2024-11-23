<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Confirmar Venta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold">¿Confirmar la venta del producto?</h3>
                    <p>Producto: <strong>{{ $product->name }}</strong></p>
                    <p>Código de Barras: <strong>{{ $product->barcode }}</strong></p>
                    <p>Precio de Venta: <strong>${{ $product->sale_price }}</strong></p>

                    <form method="POST">
                        @csrf
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-4">
                            Confirmar Venta
                        </button>
                        <a href="{{ route('productsIndex') }}" 
                           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mt-4">
                            Cancelar
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
