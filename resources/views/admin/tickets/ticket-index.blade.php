<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Listado de Tickets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Tabla de tickets -->
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold">{{ __('Tickets generados') }}</h3>
                    </div>
                    <table class="min-w-full table-auto border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <th class="border border-gray-300 px-4 py-2">{{ __('Número de Ticket') }}</th>
                                <th class="border border-gray-300 px-4 py-2">{{ __('Cliente') }}</th>
                                <th class="border border-gray-300 px-4 py-2">{{ __('Empresa del Cliente') }}</th>
                                <th class="border border-gray-300 px-4 py-2">{{ __('Monto Total') }}</th>
                                <th class="border border-gray-300 px-4 py-2">{{ __('Fecha de Creación') }}</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tickets as $ticket) <!-- Apertura del foreach -->
                                <tr class="border-b border-gray-200 dark:border-gray-600">
                                    <td class="border border-gray-300 px-4 py-2">{{ $ticket->ticket_number }}</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        {{ $ticket->sale->client_name ?? __('Sin definir') }} <!-- Verifica si el nombre del cliente está vacío -->
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        {{ $ticket->sale->client_company ?? __('Sin definir') }} <!-- Verifica si la empresa del cliente está vacía -->
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        $ {{ number_format($ticket->sale->total_amount, 2) }} <!-- Muestra el monto formateado -->
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        {{ $ticket->created_at->format('d-m-Y') }} <!-- Formato de la fecha -->
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 text-center">
                                        <a href="{{ route('tickets.show', $ticket->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-4 rounded">
                                            {{ __('Ver') }}
                                        </a>
                                        <a href="{{ route('tickets.download', $ticket->id) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-4 rounded ml-2">
                                            {{ __('Descargar_PDF') }}
                                        </a>

                                        <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST" class="inline-block ml-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-4 rounded" onclick="return confirm('{{ __('¿Estás seguro de que deseas eliminar este ticket?') }}')">
                                                {{ __('Eliminar') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach <!-- Cierre del foreach -->
                        </tbody>
                    </table>

                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $tickets->links() }} <!-- Renderizado de la paginación -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

    