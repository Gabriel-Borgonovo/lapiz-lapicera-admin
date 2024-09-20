<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Egresos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Listado de Egresos') }}</h3>

                    <a href="{{ route('admin.expenses.create') }}" class="mb-4 inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        {{ __('Registrar Egreso') }}
                    </a>

                    <!-- Filtros -->
                    <div class="mb-4">
                        <input type="text" id="search-name" placeholder="Buscar por nombre" class="px-4 py-2 rounded border">
                        <input type="date" id="start-date" class="px-4 py-2 rounded border">
                    </div>

                    <table id="expenses-table" class="w-full bg-white mb-6 hidden">
                        <thead>
                            <tr>
                                <th class="p-2 text-start">{{ __('Índice') }}</th>
                                <th class="p-2 text-start">{{ __('Descripción') }}</th>
                                <th class="p-2 text-start">{{ __('Monto') }}</th>
                                <th class="p-2 text-start">{{ __('Tipo') }}</th>
                                <th class="p-2 text-start">{{ __('Fecha') }}</th>
                                <th class="p-2 text-start">{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody id="expenses-body">
                            <!-- Los datos serán cargados aquí -->
                        </tbody>
                    </table>

                    <p id="no-expenses" class="hidden">{{ __('No hay egresos registrados.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para cargar los egresos desde la API
        function loadExpenses() {
            const name = document.getElementById('search-name').value;
            const startDate = document.getElementById('start-date').value;

            // Hacer una solicitud a la ruta de getExpenses
            fetch(`/api/expenses?name=${name}&start_date=${startDate}`)
                .then(response => response.json())
                .then(data => {
                    const table = document.getElementById('expenses-table');
                    const tbody = document.getElementById('expenses-body');
                    const noExpensesMessage = document.getElementById('no-expenses');

                    // Limpiar el contenido de la tabla
                    tbody.innerHTML = '';

                    if (data.length > 0) {
                        table.classList.remove('hidden');
                        noExpensesMessage.classList.add('hidden');

                        // Insertar las filas en la tabla, con el índice en lugar de ID
                        data.forEach((expense, index) => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="p-2">${index + 1}</td>
                                <td class="p-2">${expense.description}</td>
                                <td class="p-2">$${expense.amount}</td>
                                <td class="p-2">${expense.type}</td>
                                <td class="p-2">${new Date(expense.created_at).toLocaleDateString()}</td>
                                <td class="p-2">
                                    <a href="expenses/${expense.id}/edit" class="inline-block px-2 py-1 bg-yellow-600 text-white rounded hover:bg-yellow-700">
                                        {{ __('Editar') }}
                                    </a>
                                    <form action="expenses/${expense.id}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                            {{ __('Eliminar') }}
                                        </button>
                                    </form>
                                </td>
                            `;
                            tbody.appendChild(row);
                        });
                    } else {
                        table.classList.add('hidden');
                        noExpensesMessage.classList.remove('hidden');
                    }
                })
                .catch(error => console.error('Error al cargar los egresos:', error));
        }

        // Filtrar egresos mientras se escribe
        document.getElementById('search-name').addEventListener('keyup', loadExpenses);
        document.getElementById('start-date').addEventListener('change', loadExpenses);

        // Cargar los egresos cuando la página se cargue por primera vez
        document.addEventListener('DOMContentLoaded', loadExpenses);
    </script>
</x-app-layout>
