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
                    <!-- Buscador -->
                    <div class="mb-4">
                        <input type="text" id="client_name" placeholder="Buscar por nombre de cliente" class="mb-2 p-2 border">
                        <input type="text" id="client_company" placeholder="Buscar por empresa del cliente" class="mb-2 p-2 border">
                        <input type="date" id="created_at" class="mb-2 p-2 border">
                    </div>

                    <!-- Tabla de tickets -->
                    <table id="ticket-table" class="min-w-full table-auto border-collapse border border-gray-300">
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
                        <tbody id="ticket-body">
                            <!-- Aquí se renderizan los tickets -->
                        </tbody>
                    </table>

                    <!-- Paginación -->
                    <div class="mt-4" id="pagination"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Ejecutar búsqueda al cargar la página para mostrar todos los tickets
        document.addEventListener('DOMContentLoaded', function() {
            fetchTickets();
        });

        // Escuchar eventos de los campos para filtrar automáticamente
        document.getElementById('client_name').addEventListener('input', fetchTickets);
        document.getElementById('client_company').addEventListener('input', fetchTickets);
        document.getElementById('created_at').addEventListener('input', fetchTickets);

        function fetchTickets() {
            const clientName = document.getElementById('client_name').value;
            const clientCompany = document.getElementById('client_company').value;
            const createdAt = document.getElementById('created_at').value;

            // Construir la URL con los parámetros solo si tienen valor
            let query = `{{ route('get.tickets') }}?`;
            if (clientName) query += `client_name=${clientName}&`;
            if (clientCompany) query += `client_company=${clientCompany}&`;
            if (createdAt) query += `created_at=${createdAt}&`;

            fetch(query, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la solicitud.');
                }
                return response.json();
            })
            .then(data => {
                renderTickets(data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function renderTickets(tickets) {
            const ticketBody = document.getElementById('ticket-body');
            ticketBody.innerHTML = ''; // Limpiar la tabla antes de agregar nuevos datos

            if (tickets.length === 0) {
                ticketBody.innerHTML = '<tr><td colspan="6" class="text-center">No se encontraron tickets</td></tr>';
            } else {
                tickets.forEach(ticket => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="border border-gray-300 px-4 py-2">${ticket.ticket_number}</td>
                        <td class="border border-gray-300 px-4 py-2">${ticket.sale.client_name ?? 'Sin definir'}</td>
                        <td class="border border-gray-300 px-4 py-2">${ticket.sale.client_company ?? 'Sin definir'}</td>
                        <td class="border border-gray-300 px-4 py-2">$${parseFloat(ticket.sale.total_amount).toFixed(2)}</td>
                        <td class="border border-gray-300 px-4 py-2">${new Date(ticket.created_at).toLocaleDateString()}</td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <a href="/admin/tickets/${ticket.id}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-4 rounded">Ver</a>
                            <a href="/admin/tickets/${ticket.id}/download" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-4 rounded ml-2">PDF</a>
                            <form action="/admin/tickets/${ticket.id}" method="POST" class="inline-block ml-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-4 rounded">Eliminar</button>
                            </form>
                        </td>
                    `;
                    ticketBody.appendChild(row);
                });
            }
        }
    </script>
</x-app-layout>


    