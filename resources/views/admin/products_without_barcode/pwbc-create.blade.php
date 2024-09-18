<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Crear Producto sin Código de Barras') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('products_without_barcode.store') }}" method="POST">
                        @csrf

                        <!-- Nombre del producto -->
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Nombre del producto') }}
                            </label>
                            <input type="text" id="name" name="name" class="mt-1 block w-full p-2 border rounded-md" required>
                        </div>

                        <!-- categoria -->
                        <div class="mb-4">
                            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Categoia') }}
                            </label>
                            <input type="text" id="category" name="category" class="mt-1 block w-full p-2 border rounded-md" required>
                        </div>

                       

                        <!-- Botón para enviar el formulario -->
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Guardar producto') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>