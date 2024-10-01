<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Sale;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CashboxController extends Controller
{
    public function index()
    {
        // Obtener todas las ventas (ingresos) y egresos
        $sales = Sale::select('id', 'client_name', 'total_amount', 'created_at')
            ->get()
            ->map(function ($sale) {
                $sale->type = 'income'; // Etiquetamos como ingreso
                return $sale;
            });

        $expenses = Expense::select('id', 'description', 'amount', 'created_at', 'type')
            ->get()
            ->map(function ($expense) {
                $expense->total_amount = $expense->amount; // Unificamos campo para egreso e ingreso
                return $expense;
            });

        // Fusionar ambas colecciones y ordenarlas por fecha
        $transactions = $sales->merge($expenses)->sortBy('created_at');

        // Agrupar las transacciones por fecha
        $groupedTransactions = $transactions->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        });

        // Retornar la vista con los datos
        return view('admin.caja.cashbox-index', compact('groupedTransactions'));
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
