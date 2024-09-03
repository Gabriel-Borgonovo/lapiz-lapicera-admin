<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Método para mostrar la tabla de productos
    public function index()
    {
        return view('admin.products.products-index');
    }

    //Obtener productos en formato JSON
    public function getProducts(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');

        $query = Product::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('barcode', 'LIKE', "%$search%")//'name', 'LIKE', "%$search%"
                    ->orWhere('name', 'LIKE', "%$search%");//'barcode', 'LIKE', "%$search%"
            });
        }

        if ($category) {
            $query->where('category_id', $category);
        }

        $products = $query->paginate(10);

        return response()->json([
            'products' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ],
        ]);
    }

    public function create()
    {
        return view('admin.products.products-create');
    }

    // Método para almacenar un nuevo producto en la base de datos
    public function store(Request $request)
    {
        // Validación de datos
        $validated = $request->validate([
            'order' => 'required|unique:products',
            'barcode' => 'required|unique:products',
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category' => 'required|string|max:255',
            'unit_type' => 'required|in:unit,package',
            'purchase_price' => 'required|numeric',
            'profit_margin' => 'required|numeric',
            'sale_price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        // Manejo de la imagen (si se proporciona)
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/images/products');
            $validated['image'] = basename($imagePath);
        }

        // Creación del producto
        Product::create($validated);

        // Redirigir a la lista de productos con un mensaje de éxito
        return redirect()->route('productsIndex')->with('success', 'Product created successfully.');
    }
}
