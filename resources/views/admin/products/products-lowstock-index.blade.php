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
                    <form id="filter-form" class="mb-4" onsubmit="return false;"> <!-- Cambiado a prevent default -->
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
                    <div id="pagination" class="mt-4">
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
            const clearSearch = document.getElementById('clear-search');
            const searchInput = document.getElementById('search');
            let typingTimer; // Timer para controlar el retraso

            async function loadProducts(page = 1) {
                const search = searchInput.value.trim(); // Obtener valor directamente del campo de búsqueda

                // console.log('Valor de búsqueda:', search); // Depuración
                // console.log('URL de la solicitud:',
                //     `{{ route('getProductsWithStock') }}?page=${page}&search=${encodeURIComponent(search)}`
                // ); // URL de solicitud para verificar

                try {
                    const response = await fetch(
                        `{{ route('getProductsWithStock') }}?page=${page}&search=${encodeURIComponent(search)}`
                    );
                    const data = await response.json();

                    // Limpiar la tabla
                    tableBody.innerHTML = '';

                    // Renderizar productos en la tabla
                    data.products.forEach((product, index) => {
                        const stockStatus = product.stock === 0
                            ? '<span class="bg-red-500 text-white font-bold px-2 py-1 rounded">Sin Stock</span>'
                            : (product.stock <= 4
                                ? '<span class="bg-yellow-500 font-bold px-2 py-1 rounded">Al Límite</span>'
                                : '');

                        tableBody.innerHTML += `
                            <tr>
                                <td class="p-2 border-2">${index + 1}</td>
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

                    // Renderizar paginación
                    paginationDiv.innerHTML = '';
                    if (data.pagination.last_page > 1) {
                        for (let i = 1; i <= data.pagination.last_page; i++) {
                            paginationDiv.innerHTML += `
                                <button class="pagination-link ${i === data.pagination.current_page ? 'font-bold' : ''}" data-page="${i}">
                                    ${i}
                                </button>
                            `;
                        }
                    }
                } catch (error) {
                    console.error('Error al cargar productos:', error);
                }
            }

            // Limpiar el campo de búsqueda
            clearSearch.addEventListener('click', function() {
                searchInput.value = '';
                loadProducts();
            });

            // Cambiar de página en la paginación
            paginationDiv.addEventListener('click', function(e) {
                if (e.target.matches('.pagination-link')) {
                    const page = e.target.getAttribute('data-page');
                    loadProducts(page);
                }
            });

            // Filtrar productos automáticamente mientras se escribe
            searchInput.addEventListener('input', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    loadProducts();
                }, 500); // 500ms de retraso para evitar demasiadas solicitudes
            });

            // Manejar el evento 'focusin' para asegurar que el campo de búsqueda no pierda el foco
            searchInput.addEventListener('focusin', function() {
                searchInput.focus(); // Mantener el foco en el campo de búsqueda
            });

            // Cargar los productos al inicio
            loadProducts();
        });
    </script>

</x-app-layout>
