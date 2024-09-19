<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Producto sin Código de Barras') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Formulario para editar el producto -->
                    <form action="{{ route('products_without_barcode.update', $product->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Campo de nombre -->
                        <div class="mb-4">
                            <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Nombre del Producto') }}</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="form-input rounded-md shadow-sm mt-1 block w-full" required>
                        </div>

                        <!-- Campo de categoría -->
                        <div class="mb-4">
                            <label for="category" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Categoría') }}</label>
                            <input type="text" name="category" id="category" value="{{ old('category', $product->category) }}" class="form-input rounded-md shadow-sm mt-1 block w-full">
                        </div>

                        <!-- Botón para actualizar -->
                        <div class="flex items-center justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Actualizar Producto') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
