<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Productos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 overflow-scroll">
                    <div class="flex min-600 justify-between mb-4 items-center shadow-lg p-2 rounded">
                        <a href="{{ route('products.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Crear Producto') }}
                        </a>
                        <h3 class="text-lg font-semibold">{{ __('Productos') }}</h3>
                    </div>

                    <!-- Formulario de búsqueda y filtro -->
                    <form id="filter-form" class="mb-4">
                        <div class="flex items-center space-x-4">
                            <input type="text" name="search" id="search" class="form-input block w-full mt-1"
                                placeholder="Buscar por nombre o código de barras">
                            <button type="button" id="clear-search"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Limpiar
                            </button>
                            <select name="category" id="category" class="form-select block w-full mt-1">
                                <option value="">Todas las categorías</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}">{{ $category }}</option>
                                @endforeach
                            </select>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Filtrar</button>
                        </div>
                    </form>

                    <!-- Tabla de productos -->
                    <table id="products-table" class="w-full bg-white min-600">
                        <thead>
                            <tr>
                                <th class="p-2 text-start">{{ __('Orden') }}</th>
                                <th class="p-2 text-start">{{ __('image') }}</th>
                                <th class="p-2 text-start">{{ __('Código de Barras') }}</th>
                                <th class="p-2 text-start">{{ __('Nombre') }}</th>
                                <th class="p-2 text-start">{{ __('Categoría') }}</th>
                                <th class="p-2 text-start">{{ __('Precio de Venta') }}</th>
                                <th class="p-2 text-start">{{ __('Stock') }}</th>
                                <th class="p-2 text-center">{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Las filas de productos se cargarán aquí mediante JavaScript -->
                        </tbody>
                    </table>

                    <!-- Paginación -->
                    <div id="pagination" class="mt-4">
                        <!-- Los enlaces de paginación se cargarán aquí mediante JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('filter-form');
            const searchInput = document.getElementById('search');
            const categoryInput = document.getElementById('category');
            const productsTable = document.getElementById('products-table').getElementsByTagName('tbody')[0];
            const paginationDiv = document.getElementById('pagination');
            const clearButton = document.getElementById('clear-search'); // Nuevo botón de limpiar

            async function fetchProducts(url) {
                try {
                    const response = await fetch(url);
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    const data = await response.json();

                    // Verificar los datos obtenidos
                    console.log(data); // Esto te ayudará a ver cómo viene la respuesta JSON

                    // Limpiar la tabla y la paginación
                    productsTable.innerHTML = '';
                    paginationDiv.innerHTML = '';

                    // Obtener los datos de paginación de manera segura
                    const pagination = data.pagination || {};
                    const currentPage = pagination.current_page || 1;
                    const perPage = pagination.per_page || 10; // Valor predeterminado en caso de que no exista
                    const total = pagination.total || 0;

                    // Calcular el índice inicial en función de la página actual
                    let index = (currentPage - 1) * perPage + 1;

                    // Agregar filas a la tabla
                    data.products.forEach(product => {
                        const row = productsTable.insertRow();
                        row.innerHTML = `
                <td class="p-2">${index++}</td> <!-- Mostrar el índice aquí -->
                <td class="p-2"><img src="${product.image}" alt="imagen producto" class="w-14" /></td>
                <td class="p-2">${product.barcode}</td>
                <td class="p-2 font-black text-blue-900">${product.name}</td>
                <td class="p-2">${product.category}</td>
                <td class="p-2">${product.sale_price}</td>
                <td class="p-2">${product.stock}</td>
                <td class="p-2 text-center">
                    <a href="/admin/products/${product.id}/edit" class="inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded">Editar</a>
                    <form action="/admin/products/${product.id}" method="POST" class="inline-block" onsubmit="return confirm('¿Está seguro de eliminar este producto?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded">Eliminar</button>
                    </form>
                </td>
            `;
                    });

                    // Agregar enlaces de paginación si es necesario
                    if (pagination.last_page > 1) {
                        for (let i = 1; i <= pagination.last_page; i++) {
                            const link = document.createElement('a');
                            link.href =
                            `?page=${i}&search=${searchInput.value}&category=${categoryInput.value}`;
                            link.className =
                                'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2';
                            link.innerText = i;
                            paginationDiv.appendChild(link);
                        }
                    }
                } catch (error) {
                    console.error('Error fetching products:', error);
                }
            }


            // Manejar el envío del formulario al presionar Enter
            searchInput.addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    fetchProducts(
                        `/api/products/json?search=${searchInput.value}&category=${categoryInput.value}&page=1`
                        );
                }
            });

            // Buscar mientras escribes (con debounce)
            let timeout = null;
            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    fetchProducts(
                        `/api/products/json?search=${searchInput.value}&category=${categoryInput.value}&page=1`
                    );
                }, 300); // 300ms de debounce
            });

            // Manejar el cambio de categoría
            categoryInput.addEventListener('change', function() {
                fetchProducts(
                    `/api/products/json?search=${searchInput.value}&category=${categoryInput.value}&page=1`
                    );
            });

            // Manejar el clic en el botón de limpiar
            clearButton.addEventListener('click', function() {
                searchInput.value = ''; // Limpiar el campo de entrada
                searchInput.focus(); // Volver a enfocar el campo de entrada para la próxima búsqueda
            });

            // Cargar los productos inicialmente
            fetchProducts('/api/products/json?page=1');
        });
    </script>

</x-app-layout>
