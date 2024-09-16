<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalles del Ticket') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Información del ticket -->
                    <h3 class="text-lg font-semibold mb-4">{{ __('Ticket n°: ') }}{{ $ticket->ticket_number }}</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <p>{{ __('Venta n°: ') }}{{ $ticket->sale->id }}</p>
                        <p>{{ __('Fecha de emisión: ') }}{{ $ticket->sale->created_at->format('d-m-Y') }}</p>
                    </div>

                    <!-- Lista de productos y conceptos -->
                    <h4 class="text-lg font-semibold mt-6 mb-4">{{ __('Productos y Conceptos') }}</h4>
                    <div class="border border-gray-300 rounded-lg p-4">
                        <table class="w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <th class="p-2 text-left border-b">{{ __('Concepto') }}</th>
                                    <th class="p-2 text-right border-b">{{ __('Monto') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Productos -->
                                @foreach ($ticket->sale->saleItems as $item)
                                    <tr>
                                        <td class="p-2 border-b">
                                            {{ __('Producto: ') }}{{ $item->product->name }},
                                            {{ __('Precio unitario: $') }}{{ number_format($item->unit_price, 2) }},
                                            {{ __('Cantidad: ') }}{{ $item->quantity }}
                                        </td>
                                        <td class="p-2 border-b text-right">
                                            ${{ number_format($item->quantity * $item->unit_price, 2) }}
                                        </td>
                                    </tr>
                                @endforeach

                                <!-- Recargo o Descuento -->
                                @if ($ticket->sale->surcharge_percent > 0)
                                    <tr>
                                        <td class="p-2 border-b">
                                            {{ __('Recargo del ') }}{{ $ticket->sale->surcharge_percent }}%
                                            {{ __(' por pago con tarjeta y/o QR.') }}
                                        </td>
                                        <td class="p-2 border-b text-right">
                                            ${{ number_format($ticket->sale->total_before_adjustments * ($ticket->sale->surcharge_percent / 100), 2) }}
                                        </td>
                                    </tr>
                                @elseif ($ticket->sale->discount_percent > 0)
                                    <tr>
                                        <td class="p-2 border-b">
                                            {{ __('Descuento del ') }}{{ $ticket->sale->discount_percent }}%
                                            {{ __(' por promoción vigente.') }}
                                        </td>
                                        <td class="p-2 border-b text-right">
                                            -${{ number_format($ticket->sale->total_before_adjustments * ($ticket->sale->discount_percent / 100), 2) }}
                                        </td>
                                    </tr>
                                @endif

                            </tbody>
                            <tfoot>
                                <!-- Total -->
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <td class="p-2 text-right font-bold border-t-2 border-gray-300">
                                        {{ __('Monto Total:') }}</td>
                                    <td
                                        class="p-2 font-bold border-t-2 border-gray-300 text-right text-lg text-gray-900">
                                        ${{ number_format($ticket->sale->total_amount, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Botón para descargar el PDF -->
                    <div class="mt-6">
                        <a href="{{ route('tickets.download', $ticket->id) }}"
                            class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Descargar Ticket') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
