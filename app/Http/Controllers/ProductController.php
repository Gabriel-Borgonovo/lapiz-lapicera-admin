<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // Método para mostrar la tabla de productos
    public function index()
    {
        // Obtener todos los productos
        $products = Product::all();

        // Extraer categorías únicas de los productos
        $categories = $products->pluck('category')->unique()->sort();

        // Pasar productos y categorías a la vista
        return view('admin.products.products-index', compact('products', 'categories'));
    }

    //Obtener productos en formato JSON
    public function getProducts(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
    
        $query = Product::query();
    
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('barcode', 'LIKE', "%$search%")
                    ->orWhere('name', 'LIKE', "%$search%");
            });
        }
    
        if ($category) {
            $query->where('category', $category);
        }
    
        // Ordenar por created_at de manera descendente
        $query->orderBy('created_at', 'desc');
    
        $products = $query->paginate(10); // 10 productos por página
    
        return response()->json($products); // Devuelve toda la estructura de la paginación
    }
    



    /*************************************************************** */

    /**Manejo del stock */

    // Método para mostrar la tabla de productos con stock igual o menor a 4
    public function indexLowStock()
    {
        // Obtener productos con stock igual o menor a 4
        $products = Product::where('stock', '<=', 4)->get();

        // Extraer categorías únicas de los productos con bajo stock
        $categories = $products->pluck('category')->unique()->sort();

        // Pasar productos y categorías a la vista de productos con bajo stock
        return view('admin.products.products-lowstock-index', compact('products', 'categories'));
    }


    // Obtener productos con stock igual o menor a 4 en formato JSON
    public function getProductsWithLowStock(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');

        // Consultar solo productos con stock igual o menor a 4
        $query = Product::where('stock', '<=', 4);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('barcode', 'LIKE', "%$search%")
                    ->orWhere('name', 'LIKE', "%$search%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        // Ordenar para que los productos con stock 0 aparezcan primero
        $query->orderByRaw('stock = 0 DESC, stock ASC');

        $products = $query->paginate(10);

        return response()->json([
            'products' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ],
        ]);
    }

    public function downloadPDF()
    {
        // Obtener los productos con stock 0 o al límite (asumiendo límite de 4 unidades)
        $productosSinStock = Product::where('stock', 0)->get();
        $productosAlLimite = Product::where('stock', '<=', 4)->where('stock', '>', 0)->get();

        // Pasar los productos a la vista del PDF
        $pdf = Pdf::loadView('admin.products.productos-a-reponer-pdf', compact('productosSinStock', 'productosAlLimite'));

        // Descargar el PDF
        return $pdf->download('lista_productos_bajo_stock.pdf');
    }



    /***************************************************************** */


    //Crear y guardar productos

    public function create()
    {
        return view('admin.products.products-create');
    }

    // Método para almacenar un nuevo producto en la base de datos
    public function store(Request $request)
    {
        // Validación de datos
        $validated = $request->validate([
            'barcode' => 'required|unique:products',
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category' => 'required|string|max:255',
            'unit_type' => 'required|in:unit,package',
            'purchase_price' => 'required|numeric',
            'profit_margin' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        // Calcular el sale_price basado en purchase_price y profit_margin
        $purchasePrice = $validated['purchase_price'];
        $profitMargin = $validated['profit_margin'];
        $salePrice = $purchasePrice * (1 + ($profitMargin / 100));

        // Agregar el sale_price al array de datos validados
        $validated['sale_price'] = $salePrice;

        // Manejo de la imagen (si se proporciona)
        if ($request->hasFile('image')) {
            // Crear el directorio dinámico basado en la categoría
            $categoryDirectory = 'imgs/products/' . Str::slug($request->input('category'));

            if (env('APP_ENV') === 'production') {
                // Guardar en el disco 'custom' para producción
                $imagePath = $request->file('image')->store($categoryDirectory, 'custom');
                // Construir la URL completa de la imagen
                $validated['image'] = env('APP_URL') . '/' . $imagePath;
            } else {
                // Guardar en el disco 'public' para desarrollo
                $imagePath = $request->file('image')->store($categoryDirectory, 'public');
                // Construir la URL completa de la imagen
                $validated['image'] = asset('storage/' . $imagePath);
            }
        }

        // Creación del producto
        Product::create($validated);

        // Redirigir a la lista de productos con un mensaje de éxito
        return redirect()->route('productsIndex')->with('success', 'Product created successfully.');
    }



    //**************************************************** */



    // Editar y actualizar productos

    public function edit($id)
    {
        // Obtener el producto por su ID
        $product = Product::findOrFail($id);

        // Devolver la vista de edición con los datos del producto
        return view('admin.products.products-edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        // Validación de datos
        $validated = $request->validate([
            'barcode' => 'required|unique:products,barcode,' . $id,
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category' => 'required|string|max:255',
            'unit_type' => 'required|in:unit,package',
            'purchase_price' => 'required|numeric',
            'profit_margin' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        // Calcular el sale_price basado en purchase_price y profit_margin
        $purchasePrice = $validated['purchase_price'];
        $profitMargin = $validated['profit_margin'];
        $salePrice = $purchasePrice * (1 + ($profitMargin / 100));

        // Agregar el sale_price al array de datos validados
        $validated['sale_price'] = $salePrice;

        // Obtener el producto por su ID
        $product = Product::findOrFail($id);

        // Manejo de la imagen (si se proporciona)
        if ($request->hasFile('image')) {
            // Crear el directorio dinámico basado en la categoría
            $categoryDirectory = 'imgs/products/' . Str::slug($request->input('category'));

            // Eliminar la imagen anterior si existe
            if ($product->image) {
                // Obtener la ruta relativa de la imagen
                $existingImagePath = str_replace(
                    env('APP_ENV') === 'production' ? env('APP_URL') . '/' : asset('storage/') . '/',
                    '',
                    $product->image
                );

                // Eliminar la imagen del disco correspondiente
                if (env('APP_ENV') === 'production') {
                    Storage::disk('custom')->delete($existingImagePath);
                } else {
                    Storage::disk('public')->delete($existingImagePath);
                }
            }

            // Guardar la nueva imagen
            if (env('APP_ENV') === 'production') {
                $imagePath = $request->file('image')->store($categoryDirectory, 'custom');
                // Construir la URL completa de la imagen
                $validated['image'] = env('APP_URL') . '/' . $imagePath;
            } else {
                $imagePath = $request->file('image')->store($categoryDirectory, 'public');
                // Construir la URL completa de la imagen
                $validated['image'] = asset('storage/' . $imagePath);
            }
        }

        // Actualizar el producto con los datos validados
        $product->update($validated);

        // Redirigir a la lista de productos con un mensaje de éxito
        return redirect()->route('productsIndex')->with('success', 'Product updated successfully.');
    }




    /*************************************************************** */


    //Eliminar el producto

    public function destroy($id)
    {
        // Obtener el producto por su ID
        $product = Product::findOrFail($id);

        // Eliminar la imagen asociada si existe
        if ($product->image) {
            // Obtener la ruta relativa de la imagen
            $existingImagePath = str_replace(
                env('APP_ENV') === 'production' ? env('APP_URL') . '/' : asset('storage/') . '/',
                '',
                $product->image
            );

            // Eliminar la imagen del disco correspondiente
            if (env('APP_ENV') === 'production') {
                Storage::disk('custom')->delete($existingImagePath);
            } else {
                Storage::disk('public')->delete($existingImagePath);
            }
        }

        // Eliminar el producto
        $product->delete();

        // Redirigir a la lista de productos con un mensaje de éxito
        return redirect()->route('productsIndex')->with('success', 'Product deleted successfully.');
    }


    //Fin del controlador
}
