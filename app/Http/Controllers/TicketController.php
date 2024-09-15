<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index()
    {
        // Obtener todos los tickets con su información relacionada
        $tickets = Ticket::with('sale')->paginate(10); // Puedes ajustar la paginación si lo deseas

        // Pasar los tickets a la vista
        return view('admin.tickets.ticket-index', compact('tickets'));
    }

    // Función para eliminar ticket
    public function destroy($id)
    {
        // Encontrar el ticket
        $ticket = Ticket::findOrFail($id);

        // Verificar si el archivo existe en el disco 'public' y eliminarlo
        if (Storage::disk('public')->exists($ticket->pdf_path)) {
            Storage::disk('public')->delete($ticket->pdf_path);
        }

        // Eliminar el ticket de la base de datos
        $ticket->delete();

        // Redirigir de vuelta a la lista con un mensaje de éxito
        return redirect()->route('tickets.index')->with('success', 'Ticket eliminado correctamente.');
    }


    /************************************************************************ */
    
    // Método para generar un ticket en PDF y guardarlo
    public function generate($saleId)
    {
        // Encuentra la venta por ID
        $sale = Sale::with('saleItems.product')->findOrFail($saleId);

        // Verifica si ya existe un ticket para esta venta
        if ($sale->ticket) {
            return redirect()->route('tickets.show', $sale->ticket->id)->with('error', 'Ya existe un ticket para esta venta.');
        }

        // Generar el ticket (aquí podrías tener lógica adicional para numerar el ticket, etc.)
        $ticketNumber = uniqid('ticket_'); // O alguna otra lógica para generar el número único del ticket

        // Generar el PDF
        $pdf = Pdf::loadView('admin.tickets.ticket-pdf', compact('sale', 'ticketNumber'));

        // Guardar el archivo PDF en una ruta
        $pdfPath = 'tickets/' . $ticketNumber . '.pdf';
        $pdf->save(storage_path('app/public/' . $pdfPath));

        // Guardar la información del ticket en la base de datos
        $ticket = Ticket::create([
            'sale_id' => $sale->id,
            'ticket_number' => $ticketNumber,
            'pdf_path' => $pdfPath,
        ]);

        return redirect()->route('tickets.show', $ticket->id)->with('success', 'Ticket generado correctamente.');
    }

    // Método para mostrar un ticket específico
    public function show($id)
    {
        $ticket = Ticket::with('sale.saleItems.product')->findOrFail($id);

        return view('admin.tickets.ticket-show', compact('ticket'));
    }

    // Descargar el ticket en PDF
    public function download($id)
    {
        $ticket = Ticket::findOrFail($id);

        // Ruta del PDF
        $pdfPath = storage_path('app/public/' . $ticket->pdf_path);

        if (file_exists($pdfPath)) {
            return response()->download($pdfPath);
        }

        return redirect()->back()->with('error', 'El archivo del ticket no se encontró.');
    }
}
