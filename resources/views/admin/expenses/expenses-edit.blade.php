<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Egreso') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Editar Egreso') }}</h3>

                    <form action="{{ route('admin.expenses.update', $expense->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Descripción') }}</label>
                            <input type="text" name="description" id="description" class="block mt-1 w-full" value="{{ $expense->description }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="amount" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Monto') }}</label>
                            <input type="number" name="amount" id="amount" step="0.01" class="block mt-1 w-full" value="{{ $expense->amount }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="type" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Tipo de Egreso') }}</label>
                            <select name="type" id="type" class="block mt-1 w-full" required>
                                <option value="compra de productos" {{ $expense->type == 'compra de productos' ? 'selected' : '' }}>{{ __('Compra de productos') }}</option>
                                <option value="insumos" {{ $expense->type == 'insumos' ? 'selected' : '' }}>{{ __('Insumos') }}</option>
                                <option value="impuestos" {{ $expense->type == 'impuestos' ? 'selected' : '' }}>{{ __('Impuestos') }}</option>
                                <option value="proveedores" {{ $expense->type == 'proveedores' ? 'selected' : '' }}>{{ __('Proveedores') }}</option>
                                <option value="servicios" {{ $expense->type == 'servicios' ? 'selected' : '' }}>{{ __('Servicios') }}</option>
                                <option value="comida" {{ $expense->type == 'comida' ? 'selected' : '' }}>{{ __('Comida') }}</option>
                                <option value="transporte" {{ $expense->type == 'transporte' ? 'selected' : '' }}>{{ __('Transporte') }}</option>
                                <option value="otros" {{ $expense->type == 'otros' ? 'selected' : '' }}>{{ __('Otros') }}</option>
                            </select>
                        </div>

                        <div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">{{ __('Actualizar') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
