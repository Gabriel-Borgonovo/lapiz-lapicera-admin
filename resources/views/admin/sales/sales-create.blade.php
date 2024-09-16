<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nueva venta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Scannear Productos') }}</h3>

                    <!-- Campo de entrada para el código de barras -->
                    <input type="text" id="barcodeInput" class="border p-2 rounded"
                        placeholder="Scannear código de barras..." autofocus />

                    <!-- Tabla para listar los productos escaneados -->
                    <table class="w-full bg-white mt-4">
                        <thead>
                            <tr>
                                <th class="p-2 text-start">Product Name</th>
                                <th class="p-2 text-start">Quantity</th>
                                <th class="p-2 text-start">Unit Price</th>
                                <th class="p-2 text-start">Total Price</th>
                                <th class="p-2 text-center">Actions</th> <!-- Nueva columna para acciones -->
                            </tr>
                        </thead>
                        <tbody id="productList">
                            <!-- Aquí se insertarán las filas de productos escaneados -->
                        </tbody>
                    </table>

                    <div class="mt-4">
                        <h4 class="text-lg font-semibold">Total: <span id="totalAmount">0.00</span></h4>
                    </div>

                    <!-- Formulario para aplicar descuento y recargo -->
                    <div class="mt-6">
                        <h4 class="text-lg font-semibold mb-2">Adjustments</h4>
                        <div class="flex space-x-4">
                            <div class="flex flex-col">
                                <label for="discount" class="font-semibold">Discount (%):</label>
                                <input type="number" id="discount" class="border p-2 rounded" step="0.01"
                                    min="0" />
                            </div>
                            <div class="flex flex-col">
                                <label for="surcharge" class="font-semibold">Surcharge (%):</label>
                                <input type="number" id="surcharge" class="border p-2 rounded" step="0.01"
                                    min="0" />
                            </div>
                        </div>
                        <button id="applyAdjustments"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-4">
                            {{ __('Apply Adjustments') }}
                        </button>

                        <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mt-4"
                            onclick="finalizeSale()">
                            {{ __('Finalize Sale') }}
                        </button>

                        <!-- Campos ocultos para pasar descuentos y recargos al backend -->
                        <input type="hidden" id="discountValue" name="discountValue">
                        <input type="hidden" id="surchargeValue" name="surchargeValue">
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
            let totalAmountBeforeChanges = 0; // Total original sin cambios
            let discountPercent = 0;
            let surchargePercent = 0;

            document.addEventListener('DOMContentLoaded', function() {
                barcodeInput.focus();

                barcodeInput.addEventListener('change', async function() {
                    const barcode = barcodeInput.value;
                    console.log('Barcode Scanned:', barcode); // Para depuración
                    await fetchProduct(barcode);
                    barcodeInput.value = ''; // Limpiar el campo
                });

                // Listener para el formulario de ajuste de monto
                document.getElementById('applyAdjustments').addEventListener('click', function() {
                    const discountInput = parseFloat(document.getElementById('discount').value) || 0;
                    const surchargeInput = parseFloat(document.getElementById('surcharge').value) || 0;

                    discountPercent = discountInput;
                    surchargePercent = surchargeInput;

                    // Actualizar los campos ocultos con los valores de descuento y recargo
                    document.getElementById('discountValue').value = discountPercent;
                    document.getElementById('surchargeValue').value = surchargePercent;

                    updateTotalAmount();
                });
            });

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
                products.forEach((product, index) => { // Agregar el 'index' aquí
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
            <td class="p-2">
                <button onclick="removeProduct(${index})" class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">Eliminar</button>
            </td>
        `;
                    productList.appendChild(row);
                });
            }


            function removeProduct(index) {
                products.splice(index, 1); // Eliminar el producto del array
                renderProductList(); // Volver a renderizar la lista de productos
                updateTotalAmount(); // Actualizar el total
            }

            function updateTotalAmount() {
                // Calcular el total sin ajustes (antes de aplicar descuentos o recargos)
                totalAmountBeforeChanges = products.reduce((sum, product) => sum + parseFloat(product.totalPrice), 0).toFixed(
                    2);

                // Calcular descuento
                const discountAmount = (totalAmountBeforeChanges * (discountPercent / 100)).toFixed(2);

                // Calcular recargo
                const surchargeAmount = (totalAmountBeforeChanges * (surchargePercent / 100)).toFixed(2);

                // Calcular el total ajustado aplicando descuento y recargo
                totalAmount = (parseFloat(totalAmountBeforeChanges) - discountAmount + parseFloat(surchargeAmount)).toFixed(2);

                // Actualizar visualmente el total en la página
                totalAmountElement.innerText = `$${totalAmount}`;
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
                    alert('No hay productos en la venta.');
                    return;
                }

                const saleData = {
                    products: products,
                    totalAmountBeforeChanges: totalAmountBeforeChanges, // Total original sin ajustes
                    totalAmount: totalAmount, // Total ajustado
                    discountPercent: discountPercent,
                    surchargePercent: surchargePercent
                };

                try {
                    const response = await fetch(`${window.location.origin}/admin/sales/finalize`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify(saleData)
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (data.success) {
                        alert('¡Venta finalizada con éxito!');
                        window.location.href = data.redirect_url;
                    } else {
                        alert(data.error || 'Hubo un error al finalizar la venta.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Ocurrió un error inesperado. Verifica la consola para más detalles.');
                }
            }
        </script>



</x-app-layout>
