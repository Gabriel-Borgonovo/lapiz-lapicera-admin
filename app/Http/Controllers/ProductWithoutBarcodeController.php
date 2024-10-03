<?php

namespace App\Http\Controllers;

use App\Models\ProductWithoutBarcode;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Dompdf\Dompdf;
use Dompdf\Options;

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

        // Generar número aleatorio
        $number = str_pad(mt_rand(1, 9999999999), 10, '0', STR_PAD_LEFT);

        // Crear el producto con el número generado
        ProductWithoutBarcode::create([
            'name' => $request->name,
            'category' => $request->category,
            'number' => $number,
        ]);

        return redirect()->route('products_without_barcode.index')->with('success', 'Producto agregado exitosamente.');
    }

    // Generar un código de barras único para un producto
    public function generateBarcode($id)
    {
        $product = ProductWithoutBarcode::findOrFail($id);

        // Usar el campo 'number' para generar el código de barras
        $barcodeNumber = $product->number;

        // Crear el generador de código de barras
        $generator = new BarcodeGeneratorHTML();

        // Generar el código de barras en formato HTML
        $barcode = $generator->getBarcode($barcodeNumber, $generator::TYPE_CODE_128);

        // Almacenar el código de barras en el producto si es necesario
        $product->barcode = $barcode;
        $product->save();

        // Devolver la vista con el código de barras generado
        return view('admin.products_without_barcode.pwbc-show', compact('product', 'barcode'));
    }

    // Listar los productos sin código de barras
    public function index()
    {
        $products = ProductWithoutBarcode::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.products_without_barcode.pwbc-index', compact('products'));
    }

    // Generar PDF con todos los productos en una grilla de 3 columnas
    public function generatePdf()
    {
        $products = ProductWithoutBarcode::all();
        $generator = new BarcodeGeneratorHTML();

        $html = '<html><body><table width="100%" cellpadding="10">';
        $count = 0;

        foreach ($products as $product) {
            if ($count % 3 == 0) {
                $html .= '<tr>';
            }

            $barcodeHtml = $generator->getBarcode($product->number, $generator::TYPE_CODE_128);

            $html .= "<td>";
            $html .= "<p>{$product->name}</p>";
            $html .= $barcodeHtml;
            $html .= "<p>{$product->number}</p>";
            $html .= "</td>";

            if ($count % 3 == 2) {
                $html .= '</tr>';
            }

            $count++;
        }

        if ($count % 3 != 0) {
            $html .= '</tr>'; // Cierra la fila incompleta
        }

        $html .= '</table></body></html>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Descargar el PDF
        return $dompdf->stream('productos_codigos_barras.pdf', ['Attachment' => 1]);
    }

    // Mostrar formulario para editar un producto
    public function edit($id)
    {
        $product = ProductWithoutBarcode::findOrFail($id);
        return view('admin.products_without_barcode.pwbc-edit', compact('product'));
    }

    // Actualizar los datos del producto
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
        ]);

        $product = ProductWithoutBarcode::findOrFail($id);
        $product->update([
            'name' => $request->name,
            'category' => $request->category,
        ]);

        return redirect()->route('products_without_barcode.index')->with('success', 'Producto actualizado exitosamente.');
    }

    // Eliminar un producto de la base de datos
    public function destroy($id)
    {
        $product = ProductWithoutBarcode::findOrFail($id);
        $product->delete();

        return redirect()->route('products_without_barcode.index')->with('success', 'Producto eliminado exitosamente.');
    }
}
