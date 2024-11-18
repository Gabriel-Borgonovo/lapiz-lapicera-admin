
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

                    <!-- Filtros -->
                    <div class="mb-4">
                        <input type="date" id="start_date" class="border p-2 rounded"
                            placeholder="{{ __('Fecha inicio') }}">
                        <input type="date" id="end_date" class="border p-2 rounded"
                            placeholder="{{ __('Fecha fin') }}">
                        <button id="filter"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Buscar') }}
                        </button>
                    </div>

                    <!-- Contenedor de transacciones -->
                    <div id="transactions-container"></div>

                    
                    <div id="pagination-container" class="mt-4 flex justify-center flex-wrap"></div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.querySelector('#transactions-container');
            const filterButton = document.querySelector('#filter');

            // Cargar datos iniciales
            fetchTransactions();

            // Manejar el filtro
            filterButton.addEventListener('click', () => {
                const startDate = document.querySelector('#start_date').value;
                const endDate = document.querySelector('#end_date').value;
                fetchTransactions(startDate, endDate);
            });

            function fetchTransactions(startDate = '', endDate = '', page = 1) {
    const url = new URL('{{ route('cashbox.fetch') }}');
    if (startDate) url.searchParams.append('start_date', startDate);
    if (endDate) url.searchParams.append('end_date', endDate);
    url.searchParams.append('page', page);

    fetch(url.toString())
        .then(response => response.json())
        .then(data => {
            renderTransactions(data.data);
            renderPagination(data.current_page, data.last_page);
        })
        .catch(error => console.error('Error fetching transactions:', error));
}


            function renderTransactions(data) {
    container.innerHTML = ''; // Limpiar contenedor

    Object.keys(data).forEach(date => {
        const transactions = data[date];

        let totalIncome = 0;
        let totalExpenses = 0;

        const dateHeader = document.createElement('h4');
        dateHeader.classList.add('text-lg', 'font-semibold', 'mb-2');
        dateHeader.textContent = `Fecha: ${date}`;
        container.appendChild(dateHeader);

        const table = document.createElement('table');
        table.classList.add('w-full', 'bg-white', 'mb-6', 'border', 'border-gray-300');

        table.innerHTML = `
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-start border-b">ID</th>
                    <th class="p-2 text-start border-b">Descripción</th>
                    <th class="p-2 text-start border-b">Monto</th>
                    <th class="p-2 text-start border-b">Tipo</th>
                </tr>
            </thead>
            <tbody>
                ${transactions.map(transaction => {
                    const amount = parseFloat(transaction.total_amount) || 0;

                    if (transaction.type === 'income') {
                        totalIncome += amount;
                    } else {
                        totalExpenses += amount;
                    }

                    return `
                        <tr>
                            <td class="p-2 border-b">${transaction.id}</td>
                            <td class="p-2 border-b">
                                ${transaction.type === 'income' ? transaction.client_name || 'Venta' : transaction.description}
                            </td>
                            <td class="p-2 border-b font-bold text-gray-600">
                                ${transaction.type === 'income' ? `$${amount.toFixed(2)}` : `-$${amount.toFixed(2)}`}
                            </td>
                            <td class="p-2 border-b">
                                <span class="${transaction.type === 'income' ? 'text-green-500' : 'text-red-500'}">
                                    ${transaction.type === 'income' ? 'Ingreso' : 'Egreso'}
                                </span>
                            </td>
                        </tr>
                    `;
                }).join('')}
            </tbody>
        `;
        container.appendChild(table);

        // Resumen diario
        const summary = document.createElement('div');
        summary.innerHTML = `
            <p class="text-gray-500 mb-1">{{ __('Total de Ingresos del día: ') }} $${totalIncome.toFixed(2)}</p>
            <p class="text-gray-500 mb-1">{{ __('Total de Egresos del día: ') }} -$${totalExpenses.toFixed(2)}</p>
            <p class="text-gray-500 font-bold">{{ __('Saldo Neto del día: ') }} $${(totalIncome - totalExpenses).toFixed(2)}</p>
            <form method="POST">
                @csrf
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-2">
                    {{ __('Generar PDF') }}
                </button>
            </form>
        `;

        // Configurar acción del formulario
        const form = summary.querySelector('form');
        form.action = `{{ url('admin/caja/pdf') }}/${date}`;

        container.appendChild(summary);

        // Separador
        const divider = document.createElement('hr');
        divider.classList.add('my-4');
        container.appendChild(divider);
    });
}

function renderPagination(currentPage, lastPage) {
    const paginationContainer = document.querySelector('#pagination-container');
    paginationContainer.innerHTML = ''; // Limpia la paginación anterior

    const createButton = (text, page) => {
        const button = document.createElement('button');
        button.textContent = text;
        button.classList.add(
            'px-4', 'py-2', 'border', 'rounded-lg', 'mx-1', 'text-sm',
            'transition', 'duration-200', 'ease-in-out', 
            'focus:outline-none', 'focus:ring-2', 'focus:ring-blue-500',
            'hover:bg-blue-500', 'hover:text-white'
        );
        button.dataset.page = page;

        if (Number(page) === Number(currentPage)) { // Aseguramos que la comparación sea entre números
            button.disabled = true;
            button.classList.add(
                'bg-blue-500', 'text-white', 'cursor-not-allowed',
                'border-2', 'border-blue-600', 'shadow-lg'
            );
        } else {
            button.classList.add('bg-white', 'text-gray-700', 'hover:border-blue-400');
            button.addEventListener('click', () => fetchTransactions('', '', page));
        }

        return button;
    };

    // Botón de inicio
    if (currentPage > 1) {
        paginationContainer.appendChild(createButton('Inicio', 1));
    }

    // Calcular rango de botones visibles
    let start = Math.max(1, currentPage - 1); // Un botón antes de la página actual
    let end = Math.min(lastPage, currentPage + 1); // Un botón después de la página actual

    // Ajustar si estamos cerca del inicio o el final
    if (currentPage === 1) {
        end = Math.min(3, lastPage); // Mostrar hasta 3 botones si estamos en la primera página
    } else if (currentPage === lastPage) {
        start = Math.max(1, lastPage - 2); // Mostrar hasta 3 botones si estamos en la última página
    }

    // Crear botones para el rango visible
    for (let i = start; i <= end; i++) {
        paginationContainer.appendChild(createButton(i, i));
    }

    // Botón de fin
    if (currentPage < lastPage) {
        paginationContainer.appendChild(createButton('Fin', lastPage));
    }
}



        });
    </script>   
</x-app-layout>

