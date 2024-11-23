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
                    
                    <div class="flex justify-between items-center mb-4">
                        <div id="product-count" class="text-gray-700 text-sm"></div>
                        <div id="pagination" class="flex space-x-2"></div>
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

            // Limpiar la tabla y la paginación
            productsTable.innerHTML = '';
            paginationDiv.innerHTML = '';

            // Actualizar el contador de productos
            const productCount = document.getElementById('product-count');
            productCount.innerHTML = `Mostrando ${data.from} - ${data.to} de ${data.total} productos`;

            // Calcular el índice inicial en función de la página actual
            let index = (data.from || 0);

            // Agregar filas a la tabla
            data.data.forEach(product => {
                const row = productsTable.insertRow();
                row.innerHTML = `
                <td class="p-2">${index++}</td>
                <td class="p-2"><img src="${product.image}" alt="imagen producto" class="w-14 rounded-md shadow-sm" /></td>
                <td class="p-2">${product.barcode}</td>
                <td class="p-2 font-semibold text-blue-900 w-44">${product.name}</td>
                <td class="p-2 text-gray-600">${product.category}</td>
                <td class="p-2 text-green-600 font-semibold">$ ${product.sale_price}</td>
                <td class="p-2">${product.stock}</td>
                <td class="p-2 text-center">
                    <div class="flex justify-center items-center flex-nowrap gap-2">

                        <a href="/admin/sales/sell/${product.id}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded">Vender</a>

                        <a href="/admin/products/${product.id}/edit" class="inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded">Editar</a>
                        <form action="/admin/products/${product.id}" method="POST" class="inline-block" onsubmit="return confirm('¿Está seguro de eliminar este producto?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-3 rounded shadow">Eliminar</button>
                        </form>
                    </div>
                </td>
                `;
            });

            // Crear enlaces de paginación
            if (data.total > data.per_page) {
                const totalPages = data.last_page;
                const currentPage = data.current_page;
                const maxPagesToShow = 3;
                let startPage = Math.max(currentPage - Math.floor(maxPagesToShow / 2), 1);
                let endPage = Math.min(startPage + maxPagesToShow - 1, totalPages);

                if (endPage - startPage < maxPagesToShow - 1) {
                    startPage = Math.max(endPage - maxPagesToShow + 1, 1);
                }

                // Botón "Principio"
                if (currentPage > 1) {
                    const firstLink = document.createElement('a');
                    firstLink.href = '#';
                    firstLink.dataset.url = data.path +
                        `?page=1&search=${searchInput.value}&category=${categoryInput.value}`;
                    firstLink.className = 'bg-gray-300 hover:bg-blue-700 text-blue-700 hover:text-white font-bold py-2 px-4 rounded shadow';
                    firstLink.innerText = 'Principio';
                    firstLink.addEventListener('click', handlePaginationClick);
                    paginationDiv.appendChild(firstLink);
                }

                // Páginas numeradas
                for (let i = startPage; i <= endPage; i++) {
                    const link = document.createElement('a');
                    link.href = '#';
                    link.dataset.url = data.path +
                        `?page=${i}&search=${searchInput.value}&category=${categoryInput.value}`;
                    link.className =
                        `hover:bg-blue-700 hover:text-white font-bold py-2 px-4 rounded shadow ${i === currentPage ? 'bg-blue-700 text-white' : 'bg-gray-300 text-blue-700'}`;
                    link.innerText = i;
                    link.addEventListener('click', handlePaginationClick);
                    paginationDiv.appendChild(link);
                }

                // Botón "Final"
                if (currentPage < totalPages) {
                    const lastLink = document.createElement('a');
                    lastLink.href = '#';
                    lastLink.dataset.url = data.path +
                        `?page=${totalPages}&search=${searchInput.value}&category=${categoryInput.value}`;
                    lastLink.className = 'bg-gray-300 hover:bg-blue-700 text-blue-700 hover:text-white font-bold py-2 px-4 rounded shadow';
                    lastLink.innerText = 'Final';
                    lastLink.addEventListener('click', handlePaginationClick);
                    paginationDiv.appendChild(lastLink);
                }
            }

        } catch (error) {
            console.error('Error fetching products:', error);
        }
    }

    function handlePaginationClick(event) {
        event.preventDefault();
        const url = event.target.dataset.url;
        fetchProducts(url); // Llama nuevamente a la función fetchProducts con la nueva URL
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
