<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Egreso') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Nuevo Egreso') }}</h3>

                    <form action="{{ route('admin.expenses.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Descripción') }}</label>
                            <input type="text" name="description" id="description" class="block mt-1 w-full" required>
                        </div>

                        <div class="mb-4">
                            <label for="amount" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Monto') }}</label>
                            <input type="number" name="amount" id="amount" step="0.01" class="block mt-1 w-full" required>
                        </div>

                        <div class="mb-4">
                            <label for="type" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Tipo de Egreso') }}</label>
                            <select name="type" id="type" class="block mt-1 w-full" required>
                                <option value="compra de productos">{{ __('Compra de productos') }}</option>
                                <option value="insumos">{{ __('Insumos') }}</option>
                                <option value="impuestos">{{ __('Impuestos') }}</option>
                                <option value="proveedores">{{ __('Proveedores') }}</option>
                                <option value="servicios">{{ __('Servicios') }}</option>
                                <option value="comida">{{ __('Comida') }}</option>
                                <option value="transporte">{{ __('transporte') }}</option>
                                <option value="otros">{{ __('Otros') }}</option>
                            </select>
                        </div>

                        <div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">{{ __('Guardar') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
