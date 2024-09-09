<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Historial de ventas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Botón para crear una nueva venta -->
                    <div class="flex justify-between mb-4 items-center shadow-lg p-2 rounded">
                        <a href="{{ route('sales-create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Crear nueva venta') }}
                        </a>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ __('Historial de ventas') }}</h3>
                    </div>
                    <table class="w-full bg-white">
                        <thead>
                            <tr>
                                <th class="p-2 text-start">Venta ID</th>
                                <th class="p-2 text-start">Total</th>
                                <th class="p-2 text-start">Fecha</th>
                                <th class="p-2 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr>
                                    <td class="p-2">{{ $sale->id }}</td>
                                    <td class="p-2">$ {{ $sale->total_amount }}</td>
                                    <td class="p-2">{{ $sale->created_at->format('d-m-Y') }}</td>
                                    <td class="p-2 flex justify-center space-x-4">
                                        <!-- Enlace para ver los detalles de la venta -->
                                        <a href="{{ route('sales-show', $sale->id) }}"
                                            class="text-blue-500 hover:underline">
                                            {{ __('Ver detalles') }}
                                        </a>

                                        <!-- Formulario para eliminar la venta -->
                                        <form action="{{ route('sales-destroy', $sale->id) }}" method="POST" onsubmit="return confirm('Estás seguro que quieres eliminar esta venta?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-3 rounded">
                                                {{ __('Eliminar') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
