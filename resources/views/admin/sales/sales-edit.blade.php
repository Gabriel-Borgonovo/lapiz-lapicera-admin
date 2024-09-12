<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar venta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('sales-update', $sale->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Campo para editar el nombre del cliente -->
                        <div>
                            <label for="client_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                {{ __('Nombre del cliente') }}
                            </label>
                            <input type="text" name="client_name" id="client_name" value="{{ $sale->client_name }}" class="mt-1 p-2 w-full border rounded dark:bg-gray-700 dark:text-gray-200">
                        </div>

                        <!-- Campo para editar la empresa del cliente -->
                        <div class="mt-4">
                            <label for="client_company" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                {{ __('Empresa del cliente') }}
                            </label>
                            <input type="text" name="client_company" id="client_company" value="{{ $sale->client_company }}" class="mt-1 p-2 w-full border rounded dark:bg-gray-700 dark:text-gray-200">
                        </div>

                        <!-- Botón para guardar cambios -->
                        <div class="mt-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Guardar cambios') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>