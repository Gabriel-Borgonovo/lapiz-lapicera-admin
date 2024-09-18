<!-- En resources/views/admin/products_without_barcode/pwbc-index.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Productos sin Código de Barras') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Botón para crear un nuevo producto -->
                    <div class="flex justify-between mb-4 items-center">
                        <a href="{{ route('products_without_barcode.create') }}"
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Crear nuevo producto') }}
                        </a>
                        <a href="{{ route('products_without_barcode.pdf') }}"
                           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Generar PDF') }}
                        </a>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            {{ __('Listado de productos') }}
                        </h3>
                    </div>

                    <!-- Tabla de productos -->
                    <table class="w-full bg-white">
                        <thead>
                            <tr>
                                <th class="p-2 text-start">{{ __('ID') }}</th>
                                <th class="p-2 text-start">{{ __('Nombre') }}</th>
                                <th class="p-2 text-start">{{ __('Categoría') }}</th>
                                <th class="p-2 text-start">{{ __('Número') }}</th>
                                <th class="p-2 text-start">{{ __('Código de barras') }}</th>
                                <th class="p-2 text-center">{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr>
                                    <td class="p-2">{{ $product->id }}</td>
                                    <td class="p-2">{{ $product->name }}</td>
                                    <td class="p-2">{{ $product->category }}</td>
                                    <td class="p-2">{{ $product->number }}</td>
                                    <td class="p-2">
                                        {!! $product->barcode !!}
                                    </td>
                                    <td class="p-2 flex justify-center space-x-4">
                                        <!-- Agregar acciones como editar, eliminar, etc. -->
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">{{ __('No se encontraron productos') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Paginación -->
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
