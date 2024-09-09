<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Sale') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Scan Products') }}</h3>

                    <!-- Campo de entrada para el código de barras -->
                    <input type="text" id="barcodeInput" class="absolute" />

                    <!-- Tabla para listar los productos escaneados -->
                    <table class="w-full bg-white">
                        <thead>
                            <tr>
                                <th class="p-2 text-start">Product Name</th>
                                <th class="p-2 text-start">Quantity</th>
                                <th class="p-2 text-start">Unit Price</th>
                                <th class="p-2 text-start">Total Price</th>
                            </tr>
                        </thead>
                        <tbody id="productList">
                            <!-- Aquí se insertarán las filas de productos escaneados -->
                        </tbody>
                    </table>

                    <div class="mt-4">
                        <h4 class="text-lg font-semibold">Total: $<span id="totalAmount">0.00</span></h4>
                    </div>

                    <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mt-4"
                        onclick="finalizeSale()">
                        {{ __('Finalize Sale') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let products = [];
        const barcodeInput = document.getElementById('barcodeInput');
        const productList = document.getElementById('productList');
        const totalAmountElement = document.getElementById('totalAmount');
        let totalAmount = 0;

        document.addEventListener('DOMContentLoaded', function() {
            barcodeInput.focus();

            barcodeInput.addEventListener('change', async function() {
                const barcode = barcodeInput.value;
                console.log('Barcode Scanned:', barcode); // Añade esta línea para depuración
                await fetchProduct(barcode);
                barcodeInput.value = ''; // Limpiar el campo
            });
        });

        // Modificar la función fetchProduct para ser asíncrona
        async function fetchProduct(barcode) {
            try {
                const response = await fetch(`/admin/sales/get-product-by-barcode`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        barcode: barcode
                    })
                });

                const product = await response.json();
                console.log('Fetched Product:', product); // Añade esta línea para depuración

                if (product.error) {
                    alert(product.error);
                } else {
                    addProductToList(product);
                }
            } catch (error) {
                console.error('Fetch error:', error);
            }
        }

        function addProductToList(product) {
            const existingProduct = products.find(p => p.id === product.id);

            if (existingProduct) {
                existingProduct.quantity++;
                existingProduct.totalPrice = (existingProduct.quantity * existingProduct.unit_price).toFixed(2);
            } else {
                const newProduct = {
                    id: product.id,
                    name: product.name,
                    quantity: 1,
                    unit_price: product.sale_price,
                    totalPrice: product.sale_price
                };
                products.push(newProduct);
            }

            console.log('Products List:', products); // Añade esta línea para depuración

            renderProductList();
            updateTotalAmount();
        }

        function renderProductList() {
            productList.innerHTML = '';
            products.forEach(product => {
                const row = document.createElement('tr');
                row.innerHTML = `
            <td class="p-2">${product.name}</td>
            <td class="p-2">${product.quantity}</td>
            <td class="p-2">$${product.unit_price}</td>
            <td class="p-2">$${product.totalPrice}</td>
        `;
                productList.appendChild(row);
            });
        }

        function updateTotalAmount() {
            totalAmount = products.reduce((sum, product) => sum + parseFloat(product.totalPrice), 0).toFixed(2);
            totalAmountElement.innerText = totalAmount;
        }

        async function finalizeSale() {
            if (products.length === 0) {
                alert('No products in the sale.');
                return;
            }

            const saleData = {
                products: products,
                totalAmount: totalAmount
            };

            try {
                const response = await fetch(`/admin/sales/finalize`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify(saleData)
                });

                const data = await response.json();
                if (data.success) {
                    alert('Sale finalized successfully!');
                    window.location.href = data.redirect_url;
                } else {
                    alert(data.error || 'There was an error finalizing the sale.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An unexpected error occurred.');
            }
        }
    </script>
</x-app-layout>
