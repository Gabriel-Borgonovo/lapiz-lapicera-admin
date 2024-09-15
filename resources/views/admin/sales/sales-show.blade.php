<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sale Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Información de la venta -->
                    <h3 class="text-lg font-semibold mb-4">{{ __('Venta n°: ') }}{{ $sale->id }}</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <p>{{ __('Nombre del cliente: ') }}{{ $sale->client_name ?? 'N/A' }}</p>
                        <p>{{ __('Nombre de la Empresa: ') }}{{ $sale->client_company ?? 'N/A' }}</p>
                        <p>{{ __('Fecha: ') }}{{ $sale->created_at->format('d-m-Y') }}</p>
                    </div>

                    <!-- Lista de productos y conceptos en la venta -->
                    <h4 class="text-lg font-semibold mt-6 mb-4">{{ __('Productos y Conceptos') }}</h4>
                    <div class="border border-gray-300 rounded-lg p-4">
                        <table class="w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <th class="p-2 text-left border-b">Concepto</th>
                                    <th class="p-2 text-left border-b">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Productos -->
                                @foreach ($sale->saleItems as $item)
                                    <tr>
                                        <td class="p-2 border-b">{{ $item->product->name }} (x{{ $item->quantity }})
                                        </td>
                                        <td class="p-2 border-b">$
                                            {{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                                    </tr>
                                @endforeach

                                <!-- Recargo -->
                                @if ($sale->surcharge_percent > 0)
                                    <tr>
                                        <td class="p-2 border-b">
                                            {{ __('Recargo del ') }}{{ $sale->surcharge_percent }}%
                                            {{ __(' por pago con tarjeta y/o QR') }}
                                        </td>
                                        <td class="p-2 border-b">
                                            $
                                            {{ number_format($sale->saleItems->sum(fn($item) => $item->quantity * $item->unit_price) * ($sale->surcharge_percent / 100), 2) }}
                                        </td>
                                    </tr>
                                @endif

                                <!-- Descuento -->
                                @if ($sale->discount_percent > 0)
                                    <tr>
                                        <td class="p-2 border-b">
                                            {{ __('Descuento del ') }}{{ $sale->discount_percent }}%
                                            {{ __(' por promoción vigente') }}
                                        </td>
                                        <td class="p-2 border-b">
                                            - $
                                            {{ number_format($sale->saleItems->sum(fn($item) => $item->quantity * $item->unit_price) * ($sale->discount_percent / 100), 2) }}
                                        </td>
                                    </tr>
                                @endif

                            </tbody>
                            <tfoot>
                                <!-- Total final -->
                                <tr class="bg-gray-200 dark:bg-gray-700">
                                    <td class="p-2 text-right font-bold border-t-2 border-gray-300">
                                        {{ __('Total Amount:') }}</td>
                                    <td class="p-2 font-bold border-t-2 border-gray-300 text-lg text-gray-900">$
                                        {{ number_format($sale->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Botón para generar ticket o mensaje si ya existe -->
                    <div class="mt-6">
                        @if ($sale->ticket)
                            <!-- Ticket ya generado -->
                            <button
                                class="inline-block bg-gray-500 text-white font-bold py-2 px-4 rounded opacity-50 cursor-not-allowed"
                                disabled>
                                {{ __('Ticket generado') }}
                            </button>
                            <span class="text-gray-500 ml-2">{{ __('Esta venta ya tiene un ticket asociado.') }}</span>
                        @else
                            <!-- Botón para generar ticket si no existe -->
                            <a href="{{ route('tickets.generate', $sale->id) }}"
                                class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Generar Ticket') }}
                            </a>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
