<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Sale;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CashboxController extends Controller
{
    // public function index()
    // {
    //     // Obtener todas las ventas (ingresos) y egresos
    //     $sales = Sale::select('id', 'client_name', 'total_amount', 'created_at')
    //         ->get()
    //         ->map(function ($sale) {
    //             $sale->type = 'income'; // Etiquetamos como ingreso
    //             return $sale;
    //         });

    //     $expenses = Expense::select('id', 'description', 'amount', 'created_at', 'type')
    //         ->get()
    //         ->map(function ($expense) {
    //             $expense->total_amount = $expense->amount; // Unificamos campo para egreso e ingreso
    //             return $expense;
    //         });

    //     // Fusionar ambas colecciones y ordenarlas por fecha
    //     $transactions = $sales->merge($expenses)->sortBy('created_at');

    //     // Agrupar las transacciones por fecha
    //     $groupedTransactions = $transactions->groupBy(function ($item) {
    //         return $item->created_at->format('Y-m-d');
    //     });

    //     // Retornar la vista con los datos
    //     return view('admin.caja.cashbox-index', compact('groupedTransactions'));
    // }


    public function index()
    {
        return view('admin.caja.cashbox-index');
    }
    
    public function fetchTransactions(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $perPage = 20; // Cantidad de transacciones por página
        $currentPage = $request->input('page', 1);
    
        $sales = Sale::select('id', 'client_name', 'total_amount', 'created_at')
            ->when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate))
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($sale) => tap($sale, fn($s) => $s->type = 'income'));
    
        $expenses = Expense::select('id', 'description', 'amount', 'created_at', 'type')
            ->when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate))
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($expense) => tap($expense, fn($e) => $e->total_amount = $e->amount));
    
        $transactions = $sales->merge($expenses)->sortByDesc('created_at');
        $groupedTransactions = $transactions->groupBy(fn($item) => $item->created_at->format('Y-m-d'));
    
        // Obtener la página solicitada
        $paginatedTransactions = $groupedTransactions->slice(($currentPage - 1) * $perPage, $perPage);
    
        return response()->json([
            'data' => $paginatedTransactions,
            'current_page' => $currentPage,
            'last_page' => ceil($groupedTransactions->count() / $perPage),
        ]);
    }
    





    public function generatePDF($date)
    {
        // Obtener las transacciones de la fecha seleccionada
        $sales = Sale::whereDate('created_at', $date)->get();
        $expenses = Expense::whereDate('created_at', $date)->get();

        $totalIncome = $sales->sum('total_amount');
        $totalExpenses = $expenses->sum('amount');

        // Cargar la vista del PDF con los datos
        $pdf = Pdf::loadView('admin.caja.caja-pdf', compact('sales', 'expenses', 'totalIncome', 'totalExpenses', 'date'));

        // Descargar el PDF
        return $pdf->download("caja_{$date}.pdf");
    }
}
