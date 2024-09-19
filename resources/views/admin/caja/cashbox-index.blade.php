<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Caja') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Resumen de Caja') }}</h3>

                    <!-- Totales de ingresos y egresos -->
                    <div class="mb-6">
                        <p>{{ __('Total de Ingresos (Ventas):') }} ${{ $totalIncome }}</p>
                        <p>{{ __('Total de Egresos:') }} ${{ $totalExpenses }}</p>
                        <p>{{ __('Saldo Neto:') }} ${{ $totalIncome - $totalExpenses }}</p>
                    </div>

                    <!-- Tabla de ingresos (Ventas) -->
                    <h4 class="text-lg font-semibold">{{ __('Ingresos (Ventas)') }}</h4>
                    <table class="w-full bg-white mb-6">
                        <thead>
                            <tr>
                                <th class="p-2 text-start">{{ __('ID Venta') }}</th>
                                <th class="p-2 text-start">{{ __('Cliente') }}</th>
                                <th class="p-2 text-start">{{ __('Monto Total') }}</th>
                                <th class="p-2 text-start">{{ __('Fecha') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr>
                                    <td class="p-2">{{ $sale->id }}</td>
                                    <td class="p-2">{{ $sale->client_name ?? 'No especificado' }}</td>
                                    <td class="p-2">${{ $sale->total_amount }}</td>
                                    <td class="p-2">{{ $sale->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Tabla de egresos -->
                    <h4 class="text-lg font-semibold">{{ __('Egresos') }}</h4>
                    <table class="w-full bg-white">
                        <thead>
                            <tr>
                                <th class="p-2 text-start">{{ __('ID Egreso') }}</th>
                                <th class="p-2 text-start">{{ __('Descripción') }}</th>
                                <th class="p-2 text-start">{{ __('Monto') }}</th>
                                <th class="p-2 text-start">{{ __('Tipo') }}</th>
                                <th class="p-2 text-start">{{ __('Fecha') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($expenses as $expense)
                                <tr>
                                    <td class="p-2">{{ $expense->id }}</td>
                                    <td class="p-2">{{ $expense->description }}</td>
                                    <td class="p-2">${{ $expense->amount }}</td>
                                    <td class="p-2">{{ $expense->type }}</td>
                                    <td class="p-2">{{ $expense->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
