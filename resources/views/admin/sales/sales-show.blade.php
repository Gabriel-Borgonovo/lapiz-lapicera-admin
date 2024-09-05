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
                    <h3 class="text-lg font-semibold">{{ __('Sale ID: ') }}{{ $sale->id }}</h3>
                    <p>{{ __('Client Name: ') }}{{ $sale->client_name ?? 'N/A' }}</p>
                    <p>{{ __('Client Company: ') }}{{ $sale->client_company ?? 'N/A' }}</p>
                    <p>{{ __('Total Amount: $') }}{{ $sale->total_amount }}</p>
                    <p>{{ __('Date: ') }}{{ $sale->created_at->format('d-m-Y') }}</p>

                    <!-- Lista de productos en la venta -->
                    <h4 class="text-lg font-semibold mt-4">{{ __('Products') }}</h4>
                    <table class="w-full bg-white">
                        <thead>
                            <tr>
                                <th class="p-2 text-start">Product Name</th>
                                <th class="p-2 text-start">Quantity</th>
                                <th class="p-2 text-start">Unit Price</th>
                                <th class="p-2 text-start">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale->saleItems as $item)
                                <tr>
                                    <td class="p-2">{{ $item->product->name }}</td>
                                    <td class="p-2">{{ $item->quantity }}</td>
                                    <td class="p-2">$ {{ $item->unit_price }}</td>
                                    <td class="p-2">$ {{ $item->quantity * $item->unit_price }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>