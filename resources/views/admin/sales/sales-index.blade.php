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
                    <!-- Buscador -->
                    <div class="mb-4">
                        <input type="date" id="sale_date" class="mb-2 p-2 border">
                        <input type="text" id="total_amount" placeholder="Buscar por monto total" class="mb-2 p-2 border">
                    </div>

                    <!-- Botón para crear una nueva venta -->
                    <div class="flex justify-between mb-4 items-center shadow-lg p-2 rounded">
                        <a href="{{ route('sales-create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Crear nueva venta') }}
                        </a>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            {{ __('Historial de ventas') }}</h3>
                    </div>

                    <!-- Tabla de ventas -->
                    <table id="sales-table" class="w-full bg-white">
                        <thead>
                            <tr>
                                <th class="p-2 text-start">Venta ID</th>
                                <th class="p-2 text-start">Total</th>
                                <th class="p-2 text-start">Fecha</th>
                                <th class="p-2 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="sales-body">
                            <!-- Aquí se renderizan las ventas -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchSales();
        });

        document.getElementById('sale_date').addEventListener('input', fetchSales);
        document.getElementById('total_amount').addEventListener('input', fetchSales);

        function fetchSales() {
            const saleDate = document.getElementById('sale_date').value;
            const totalAmount = document.getElementById('total_amount').value;

            let query = `{{ route('sales.get') }}?`;
            if (saleDate) query += `sale_date=${saleDate}&`;
            if (totalAmount) query += `total_amount=${totalAmount}&`;

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
                renderSales(data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function renderSales(sales) {
            const salesBody = document.getElementById('sales-body');
            salesBody.innerHTML = ''; // Limpiar la tabla antes de agregar nuevos datos

            if (sales.length === 0) {
                salesBody.innerHTML = '<tr><td colspan="4" class="text-center">No se encontraron ventas</td></tr>';
            } else {
                sales.forEach(sale => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="p-2">${sale.id}</td>
                        <td class="p-2">$ ${sale.total_amount}</td>
                        <td class="p-2">${new Date(sale.created_at).toLocaleDateString()}</td>
                        <td class="p-2 flex justify-center space-x-4">
                            <a href="/sales/${sale.id}" class="text-blue-500 hover:underline">Ver detalles</a>
                            <a href="/sales/${sale.id}/edit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-3 rounded">Editar</a>
                            <form action="/admin/sales/${sale.id}" method="POST" onsubmit="return confirm('Estás seguro que quieres eliminar esta venta?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-3 rounded">Eliminar</button>
                            </form>
                        </td>
                    `;
                    salesBody.appendChild(row);
                });
            }
        }
    </script>
</x-app-layout>
