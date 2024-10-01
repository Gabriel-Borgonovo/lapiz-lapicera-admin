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

                    @foreach ($groupedTransactions as $date => $transactions)
                        <h4 class="text-lg font-semibold mb-2">{{ __('Fecha: ') }} {{ $date }}</h4>
                        
                        <!-- Tabla de ingresos y egresos agrupados por fecha -->
                        <table class="w-full bg-white mb-6">
                            <thead>
                                <tr>
                                    <th class="p-2 text-start">{{ __('ID') }}</th>
                                    <th class="p-2 text-start">{{ __('Descripción') }}</th>
                                    <th class="p-2 text-start">{{ __('Monto') }}</th>
                                    <th class="p-2 text-start">{{ __('Tipo') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalIncome = 0;
                                    $totalExpenses = 0;
                                @endphp
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td class="p-2">{{ $transaction->id }}</td>
                                        <td class="p-2">
                                            @if($transaction->type == 'income')
                                                {{ $transaction->client_name ?? 'Venta' }}
                                            @else
                                                {{ $transaction->description }}
                                            @endif
                                        </td>
                                        <td class="p-2 font-bold text-gray-600">
                                            @if($transaction->type == 'income')
                                                ${{ $transaction->total_amount }}
                                            @else
                                                -${{ $transaction->total_amount }}
                                            @endif
                                        </td>
                                        <td class="p-2">
                                            @if($transaction->type == 'income')
                                               <span class="text-green-500">{{ __('Ingreso') }}</span> 
                                                @php $totalIncome += $transaction->total_amount; @endphp
                                            @else
                                                <span class="text-red-500">{{ __('Egreso') }}</span>
                                                @php $totalExpenses += $transaction->total_amount; @endphp
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Mostrar el resumen del día -->
                        <p>{{ __('Total de Ingresos del día: ') }} ${{ $totalIncome }}</p>
                        <p>{{ __('Total de Egresos del día: ') }} -${{ $totalExpenses }}</p>
                        <p>{{ __('Saldo Neto del día: ') }} ${{ $totalIncome - $totalExpenses }}</p>

                        <!-- Botón para generar PDF -->
                        <form action="{{ route('cashbox.generatePDF', ['date' => $date]) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-2">
                                {{ __('Generar PDF') }}
                            </button>
                        </form>
                        <hr class="my-4">
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
