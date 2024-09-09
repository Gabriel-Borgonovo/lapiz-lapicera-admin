<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('products.update', $product->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="order"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">Número de orden del producto</label>
                            <input type="text" id="order" name="order"
                                value="{{ old('order', $product->order) }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('order') border-red-500 @enderror">
                            @error('order')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="barcode"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">Código de barras del producto</label>
                            <input type="text" id="barcode" name="barcode"
                                value="{{ old('barcode', $product->barcode) }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('barcode') border-red-500 @enderror">
                            @error('barcode')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nombre del producto</label>
                            <input type="text" id="name" name="name"
                                value="{{ old('name', $product->name) }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="image"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">Imagen</label>
                            <input type="file" id="image" name="image" class="mt-1 block w-full">
                            @if ($product->image)
                                
                                <img src="{{ $product->image }}" alt="Product Image" class="mt-2 image-edit">
                                
                            @endif
                            @error('image')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="category"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">Categoria</label>
                            <input type="text" id="category" name="category"
                                value="{{ old('category', $product->category) }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('category') border-red-500 @enderror">
                            @error('category')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="unit_type"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">Tipo de unidad del producto</label>
                            <select id="unit_type" name="unit_type"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('unit_type') border-red-500 @enderror">
                                <option value="unit"
                                    {{ old('unit_type', $product->unit_type) == 'unit' ? 'selected' : '' }}>Unidad
                                </option>
                                <option value="package"
                                    {{ old('unit_type', $product->unit_type) == 'package' ? 'selected' : '' }}>Paquete
                                </option>
                            </select>
                            @error('unit_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="stock"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">Stock</label>
                            <input type="number" id="stock" name="stock"
                                value="{{ old('stock', $product->stock) }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('stock') border-red-500 @enderror">
                            @error('stock')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="purchase_price"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">Precio de compra</label>
                            <input type="number" step="0.01" id="purchase_price" name="purchase_price"
                                value="{{ old('purchase_price', $product->purchase_price) }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('purchase_price') border-red-500 @enderror">
                            @error('purchase_price')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="profit_margin"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-400">Margen de ganancia</label>
                            <input type="number" step="0.01" id="profit_margin" name="profit_margin"
                                value="{{ old('profit_margin', $product->profit_margin) }}"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('profit_margin') border-red-500 @enderror">
                            @error('profit_margin')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        

                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ __('Actualizar') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
