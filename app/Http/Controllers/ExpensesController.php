<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    public function index()
    {
        // Solo retorna la vista sin los datos, los datos se cargarán por AJAX
        return view('admin.expenses.expenses-index');
    }

    // Método para obtener los egresos en formato JSON con filtros
    public function getExpenses(Request $request)
    {
        // Crear la consulta base
        $query = Expense::query();
    
        // Filtrar por nombre (descripción)
        if ($request->has('name') && $request->input('name') != '') {
            $query->where('description', 'like', '%' . $request->input('name') . '%');
        }
    
        // Filtrar por fecha de creación específica
        if ($request->has('start_date') && $request->input('start_date') != '') {
            $query->whereDate('created_at', $request->input('start_date'));
        }
    
        // Obtener los egresos filtrados
        $expenses = $query->get();
    
        // Devolver la respuesta en formato JSON
        return response()->json($expenses);
    }

    // Mostrar formulario para crear un nuevo egreso
    public function create()
    {
        return view('admin.expenses.expenses-create');
    }

    // Guardar un nuevo egreso
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required',
            'amount' => 'required|numeric',
            'type' => 'required',
        ]);

        Expense::create($request->all());

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Egreso creado exitosamente.');
    }

    // Mostrar formulario para editar un egreso
    public function edit(Expense $expense)
    {
        return view('admin.expenses.expenses-edit', compact('expense'));
    }

    // Actualizar un egreso
    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'description' => 'required',
            'amount' => 'required|numeric',
            'type' => 'required',
        ]);

        $expense->update($request->all());

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Egreso actualizado exitosamente.');
    }

    // Eliminar un egreso
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Egreso eliminado exitosamente.');
    }
}
