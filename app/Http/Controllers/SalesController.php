<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    // Mostrar la lista de ventas
    public function index()
    {
        $sales = Sale::with('saleItems.product')->get();
        return view('admin.sales.sales-index', compact('sales'));
    }

    public function create()
    {
        $sales = Sale::with('saleItems.product')->get();
        return view('admin.sales.sales-create', compact('sales'));
    }

     // Mostrar detalles de una venta específica
     public function show($id)
     {
         // Cargar la venta junto con los ítems y productos asociados
         $sale = Sale::with('saleItems.product')->findOrFail($id);
         return view('admin.sales.sales-show', compact('sale'));
     }

    // Obtener detalles de un producto por código de barras
    public function getProductByBarcode(Request $request)
    {
        $barcode = $request->input('barcode');
        $product = Product::where('barcode', $barcode)->first();

        if ($product) {
            return response()->json($product);
        } else {
            return response()->json(['error' => 'Producto no encontrado.'], 404);
        }
    }

    // Finalizar una venta y almacenar en la base de datos
    public function finalizeSale(Request $request)
    {
        $data = $request->validate([
            'products' => 'required|array',
            'totalAmount' => 'required|numeric'
        ]);
    
        // Crear la venta
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado.'], 401);
        }
    
        $sale = Sale::create([
            'user_id' => $user->id,
            'total_amount' => $data['totalAmount']
        ]);
    
        // Agregar los productos a la venta
        foreach ($data['products'] as $productData) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $productData['id'],
                'quantity' => $productData['quantity'],
                'unit_price' => $productData['unit_price']
            ]);
    
            // Restar la cantidad de productos comprados del stock
            $product = Product::find($productData['id']);
            $product->decrement('stock', $productData['quantity']);
        }
    
        // Devolver una respuesta JSON para manejar la redirección en el front-end
        return response()->json(['success' => true, 'redirect_url' => route('sales.index')]);
    }
}
