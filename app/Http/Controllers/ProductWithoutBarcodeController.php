<?php

namespace App\Http\Controllers;

use App\Models\ProductWithoutBarcode;
use Illuminate\Http\Request;

class ProductWithoutBarcodeController extends Controller
{
     // Mostrar formulario para agregar un nuevo producto sin código de barras
     public function create()
     {
         return view('admin.products_without_barcode.pwbc-create');
     }
 
     // Guardar el producto en la base de datos
     public function store(Request $request)
     {
         $request->validate([
             'name' => 'required|string|max:255',
             'category' => 'nullable|string|max:255',
             
         ]);
 
         ProductWithoutBarcode::create($request->all());
 
         return redirect()->route('products_without_barcode.index')->with('success', 'Producto agregado exitosamente.');
     }
 
     // Generar un código de barras único para un producto
     public function generateBarcode($id)
     {
         $product = ProductWithoutBarcode::findOrFail($id);
 
         // Generar un código de barras único (números aleatorios)
         $barcode = str_pad(mt_rand(1, 9999999999), 10, '0', STR_PAD_LEFT);
 
         // Asignar el código de barras al producto
         $product->barcode = $barcode;
         $product->save();
 
         return redirect()->back()->with('success', 'Código de barras generado exitosamente.');
     }
 
     // Listar los productos sin código de barras
     public function index()
     {
         $products = ProductWithoutBarcode::all();
         return view('admin.products_without_barcode.pwbc-index', compact('products'));
     }
}
