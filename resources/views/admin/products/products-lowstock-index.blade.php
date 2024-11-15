<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Productos con Bajo Stock') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 overflow-scroll">
                    <div class="flex min-600 justify-between mb-4 items-center shadow-lg p-2 rounded">
                        <h3 class="text-lg font-semibold">{{ __('Productos con Bajo Stock') }}</h3>
                        <a href="{{ route('productos.pdf') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Descargar Lista PDF
                        </a>
                    </div>

                    <!-- Formulario de búsqueda y filtro -->
                    <form id="filter-form" class="mb-4" onsubmit="return false;">
                        <div class="flex items-center space-x-4">
                            <input type="text" name="search" id="search" class="form-input block w-full mt-1"
                                placeholder="Buscar por nombre o código de barras">
                            <button type="button" id="clear-search"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Limpiar
                            </button>
                        </div>
                    </form>

                    <!-- Tabla de productos -->
                    <table id="products-table" class="w-full bg-white min-600">
                        <thead>
                            <tr>
                                <th class="p-2 text-start border-2">{{ __('Orden') }}</th>
                                <th class="p-2 text-start border-2">{{ __('Imagen') }}</th>
                                <th class="p-2 text-start border-2">{{ __('Código de Barras') }}</th>
                                <th class="p-2 text-start border-2">{{ __('Nombre') }}</th>
                                <th class="p-2 text-start border-2">{{ __('Stock') }}</th>
                                <th class="p-2 text-center border-2">{{ __('Estado') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Las filas de productos con bajo stock se cargarán aquí mediante JavaScript -->
                        </tbody>
                    </table>

                    <!-- Paginación -->
                    <div id="pagination-info" class="text-gray-700 mb-2"></div>
                    <div id="pagination" class="mt-4 flex justify-center space-x-2">
                        <!-- Los enlaces de paginación se cargarán aquí mediante JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts para cargar productos mediante AJAX -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('filter-form');
            const tableBody = document.querySelector('#products-table tbody');
            const paginationDiv = document.getElementById('pagination');
            const paginationInfo = document.getElementById('pagination-info');
            const clearSearch = document.getElementById('clear-search');
            const searchInput = document.getElementById('search');
            const itemsPerPage = 10;
            let typingTimer;

            async function loadProducts(page = 1) {
                const search = searchInput.value.trim();
                try {
                    const response = await fetch(`{{ route('getProductsWithStock') }}?page=${page}&search=${encodeURIComponent(search)}`);
                    const data = await response.json();

                    tableBody.innerHTML = '';
                    data.products.forEach((product, index) => {
                        const orderNumber = (page - 1) * itemsPerPage + (index + 1);

                        const stockStatus = product.stock === 0
                            ? '<span class="bg-red-500 text-white font-bold px-2 py-1 rounded">Sin Stock</span>'
                            : (product.stock <= 4
                                ? '<span class="bg-yellow-500 font-bold px-2 py-1 rounded">Al Límite</span>'
                                : '');

                        tableBody.innerHTML += `
                            <tr>
                                <td class="p-2 border-2">${orderNumber}</td>
                                <td class="p-2 border-2">
                                    <img src="${product.image}" alt="${product.name}" class="w-16 h-16 object-cover">
                                </td>
                                <td class="p-2 border-2">${product.barcode}</td>
                                <td class="p-2 border-2">${product.name}</td>
                                <td class="p-2 border-2">${product.stock}</td>
                                <td class="p-2 border-2 text-center">${stockStatus}</td>
                            </tr>
                        `;
                    });

                     // Mostrar el rango actual de productos y el total
        const totalProducts = data.pagination.total;
        const startProduct = (page - 1) * itemsPerPage + 1;
        const endProduct = Math.min(page * itemsPerPage, totalProducts);

        paginationInfo.textContent = `Mostrando productos ${startProduct} a ${endProduct} de ${totalProducts} en total`;

                    // Configuración de la paginación
                    paginationDiv.innerHTML = '';
                    if (data.pagination.last_page > 1) {
                        const currentPage = data.pagination.current_page;
                        const lastPage = data.pagination.last_page;
                        const paginationLinks = [];

                        // Botón "Primero"
                        if (currentPage > 1) {
                            paginationLinks.push(`
                                <button class="pagination-button bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-l" data-page="1">
                                    Primero
                                </button>
                            `);
                        }

                        // Números de página, máximo 3 visibles
                        let startPage = Math.max(currentPage - 1, 1);
                        let endPage = Math.min(currentPage + 1, lastPage);

                        if (currentPage === 1) {
                            endPage = Math.min(3, lastPage);
                        } else if (currentPage === lastPage) {
                            startPage = Math.max(lastPage - 2, 1);
                        }

                        for (let i = startPage; i <= endPage; i++) {
                            paginationLinks.push(`
                                <button class="pagination-button ${i === currentPage ? 'bg-blue-500 text-white' : 'bg-gray-300 hover:bg-gray-400 text-gray-800'} font-semibold py-2 px-4" data-page="${i}">
                                    ${i}
                                </button>
                            `);
                        }

                        // Botón "Último"
                        if (currentPage < lastPage) {
                            paginationLinks.push(`
                                <button class="pagination-button bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-r" data-page="${lastPage}">
                                    Último
                                </button>
                            `);
                        }

                        paginationDiv.innerHTML = paginationLinks.join('');
                    }
                } catch (error) {
                    console.error('Error al cargar productos:', error);
                }
            }

            clearSearch.addEventListener('click', function() {
                searchInput.value = '';
                loadProducts();
            });

            paginationDiv.addEventListener('click', function(e) {
                if (e.target.matches('.pagination-button')) {
                    const page = e.target.getAttribute('data-page');
                    loadProducts(page);
                }
            });

            searchInput.addEventListener('input', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    loadProducts();
                }, 500);
            });

            loadProducts();
        });
    </script>
</x-app-layout>


