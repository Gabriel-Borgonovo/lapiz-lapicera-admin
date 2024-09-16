<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SalesController extends Controller
{
    // Mostrar la lista de ventas
    public function index()
    {
        // Solo devuelve la vista sin los datos
        return view('admin.sales.sales-index');
    }



    /********************************************** */


    public function getSales(Request $request)
    {
        $query = Sale::with('saleItems.product');

        // Filtro por fecha de venta
        if ($request->filled('sale_date')) {
            // Convertir la fecha de la solicitud al formato 'Y-m-d' y agregar las horas de inicio y fin del día
            $date = date('Y-m-d', strtotime($request->sale_date));
            $startOfDay = $date . ' 00:00:00';
            $endOfDay = $date . ' 23:59:59';

            $query->whereBetween('created_at', [$startOfDay, $endOfDay]);
        }

        // Filtro por monto total
        if ($request->filled('total_amount')) {
            $query->where('total_amount', $request->total_amount);
        }

        $sales = $query->orderBy('created_at', 'desc')->get();

        return response()->json($sales);
    }






    /***************************** */
    /**Editar una venta */
    public function edit($id)
    {
        $sale = Sale::findOrFail($id);
        return view('admin.sales.sales-edit', compact('sale'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'client_name' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
        ]);

        $sale = Sale::findOrFail($id);
        $sale->update($data);

        return redirect()->route('sales.index')->with('success', 'Venta actualizada correctamente.');
    }

    /************************************************ */
    /** Eliminar una venta */
    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);

        // Reintegra los elementos asociados a la venta al stock
        foreach ($sale->saleItems as $saleItem) {
            $product = Product::find($saleItem->product_id);
            if ($product) {
                $product->increment('stock', $saleItem->quantity);
            }
        }

        // Elimina el ticket asociado a la venta (si existe)
        if ($sale->ticket) {
            // Elimina el archivo PDF del ticket si existe
            if ($sale->ticket->pdf_path && Storage::disk('public')->exists($sale->ticket->pdf_path)) {
                Storage::disk('public')->delete($sale->ticket->pdf_path); // Eliminamos el archivo del disco 'public'
            }

            // Elimina el registro del ticket
            $sale->ticket->delete();
        }

        // Elimina los elementos asociados a la venta
        $sale->saleItems()->delete();

        // Elimina la venta
        $sale->delete();

        return redirect()->route('sales.index')->with('success', 'Sale, associated ticket, PDF, and stock restored successfully.');
    }


    /******************************************** */

    //vista para crear una venta
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
            if ($product->stock > 0) {
                return response()->json($product);
            } else {
                return response()->json(['error' => 'El producto no tiene stock disponible.'], 400);
            }
        } else {
            return response()->json(['error' => 'Producto no encontrado.'], 404);
        }
    }

    // Finalizar una venta y almacenar en la base de datos

    public function finalizeSale(Request $request)
    {
        Log::info('Inicio del método finalizeSale');

        $data = $request->validate([
            'products' => 'required|array',
            'totalAmountBeforeChanges' => 'required|numeric', // Total antes de ajustes
            'totalAmount' => 'required|numeric', // Total después de ajustes
            'discountPercent' => 'nullable|numeric',
            'surchargePercent' => 'nullable|numeric'
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado.'], 401);
        }

        $totalBeforeAdjustments = $data['totalAmountBeforeChanges']; // Total original sin ajustes
        $discountPercent = $data['discountPercent'] ?? 0;
        $surchargePercent = $data['surchargePercent'] ?? 0;

        // Calcula descuento y recargo para control (en el backend)
        $discountAmount = ($totalBeforeAdjustments * $discountPercent) / 100;
        $surchargeAmount = ($totalBeforeAdjustments * $surchargePercent) / 100;

        // Verifica que el total ajustado del frontend coincide con lo calculado en el backend
        $calculatedTotal = $totalBeforeAdjustments - $discountAmount + $surchargeAmount;
        if (abs($calculatedTotal - $data['totalAmount']) > 0.01) {
            return response()->json(['error' => 'El total ajustado no coincide con el cálculo.'], 400);
        }

        // Crear la venta
        $sale = Sale::create([
            'user_id' => $user->id,
            'total_amount' => $data['totalAmount'], // Total después de ajustes
            'discount_percent' => $discountPercent,
            'surcharge_percent' => $surchargePercent,
            'total_before_adjustments' => $totalBeforeAdjustments, // Total antes de ajustes
        ]);

        foreach ($data['products'] as $productData) {
            $product = Product::find($productData['id']);

            if (!$product) {
                return response()->json(['error' => 'Product not found: ' . $productData['id']], 400);
            }

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $productData['id'],
                'quantity' => $productData['quantity'],
                'unit_price' => $productData['unit_price']
            ]);

            $product->decrement('stock', $productData['quantity']);
        }

        return response()->json(['success' => true, 'redirect_url' => route('sales.index')]);
    }
}
