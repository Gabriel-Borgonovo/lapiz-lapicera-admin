<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Sale;
use Illuminate\Http\Request;

class CashboxController extends Controller
{
    public function index()
    {
        // Obtener todas las ventas (ingresos)
        $sales = Sale::with('saleItems.product')->get();

        // Obtener todos los egresos
        $expenses = Expense::all();

        // Calcular el total de ingresos y egresos
        $totalIncome = $sales->sum('total_amount');
        $totalExpenses = $expenses->sum('amount');

        // Retornar la vista con los datos
        return view('admin.caja.cashbox-index', compact('sales', 'expenses', 'totalIncome', 'totalExpenses'));
    }
}
