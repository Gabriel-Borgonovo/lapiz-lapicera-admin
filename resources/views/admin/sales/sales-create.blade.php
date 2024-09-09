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
                console.log('Barcode Scanned:', barcode); // Para depuración
                await fetchProduct(barcode);
                barcodeInput.value = ''; // Limpiar el campo
            });
        });
    
        async function fetchProduct(barcode) {
            try {
                const response = await fetch(`/admin/sales/get-product-by-barcode`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ barcode: barcode })
                });
    
                const product = await response.json();
                console.log('Fetched Product:', product); // Para depuración
    
                if (product.error) {
                    alert(product.error); // Mostrar mensaje de error si no hay stock
                } else if (product.stock <= 0) {
                    alert('El producto no tiene stock disponible.'); // Verificar el stock en el frontend
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
                if (existingProduct.quantity < product.stock) {
                    existingProduct.quantity++;
                    existingProduct.totalPrice = (existingProduct.quantity * existingProduct.unit_price).toFixed(2);
                } else {
                    alert(`No hay suficiente stock disponible para ${product.name}. Stock disponible: ${product.stock}.`);
                }
            } else {
                if (product.stock > 0) {
                    const newProduct = {
                        id: product.id,
                        name: product.name,
                        quantity: 1,
                        unit_price: product.sale_price,
                        stock: product.stock, // Almacenar el stock para validaciones
                        totalPrice: product.sale_price
                    };
                    products.push(newProduct);
                } else {
                    alert('El producto no tiene stock disponible.');
                }
            }
    
            console.log('Products List:', products); // Para depuración
            renderProductList();
            updateTotalAmount();
        }
    
        function renderProductList() {
            productList.innerHTML = '';
            products.forEach(product => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="p-2">${product.name}</td>
                    <td class="p-2 flex items-center space-x-2">
                        <button onclick="decreaseQuantity(${product.id})" class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded">-</button>
                        <span class="text-center w-8">${product.quantity}</span>
                        <button onclick="increaseQuantity(${product.id})" class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-2 rounded">+</button>
                    </td>
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
    
        function increaseQuantity(productId) {
            const product = products.find(p => p.id === productId);
            if (product.quantity < product.stock) {
                product.quantity++;
                product.totalPrice = (product.quantity * product.unit_price).toFixed(2);
            } else {
                alert(`No hay suficiente stock disponible para ${product.name}. Stock disponible: ${product.stock}.`);
            }
            renderProductList();
            updateTotalAmount();
        }
    
        function decreaseQuantity(productId) {
            const product = products.find(p => p.id === productId);
            if (product.quantity > 1) {
                product.quantity--;
                product.totalPrice = (product.quantity * product.unit_price).toFixed(2);
            } else {
                alert('No puedes tener menos de 1 producto.');
            }
            renderProductList();
            updateTotalAmount();
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
