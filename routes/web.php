<?php

use App\Http\Controllers\CashboxController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductWithoutBarcodeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/test-log', function () {
//     Log::info('Este es un mensaje de prueba en el log.');
//     return 'Mensaje de log enviado.';
// });

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['role:Admin'])->group(function () {
        //usuarios
        Route::get('/admin/users', [UserController::class, 'index'])->name('users');
        Route::delete('/admin/users/destroy/{id}', [UserController::class, 'destroy'])->name('users-destroy');
        Route::get('/admin/users/{user}/edit-role', [UserController::class, 'editRole'])->name('users-edit-role');
        Route::put('/admin/users/{user}/update-role', [UserController::class, 'updateRole'])->name('users-update-role');

        //roles
        Route::get('/admin/roles', [RoleController::class, 'index'])->name('roles');
        Route::get('/admin/roles/create', [RoleController::class, 'create'])->name('roles-create');
        Route::post('/admin/roles/store', [RoleController::class, 'store'])->name('roles-store');
        Route::get('/admin/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles-edit');
        Route::delete('/admin/roles/destroy/{id}', [RoleController::class, 'destroy'])->name('roles-destroy');
        Route::put('/admin/roles/{role}/update-role', [RoleController::class, 'update'])->name('roles-update');

        //permissions
        Route::resource('permissions', PermissionController::class);
    });

    //products
    // Ruta para la vista de productos
    Route::get('admin/products', [ProductController::class, 'index'])->name('productsIndex');
    Route::get('/api/products/json', [ProductController::class, 'getProducts'])->name('products.json');
    Route::get('/api/products-stock', [ProductController::class, 'getProductsWithLowStock'])->name('getProductsWithStock');

    // Otras rutas relacionadas con productos, como crear, editar, eliminar, etc.
    Route::get('admin/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('admin/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('admin/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::get('admin/products-stock', [ProductController::class, 'indexLowStock'])->name('productsStockIndex');
    Route::get('admin/products-stock/pdf', [ProductController::class, 'downloadPDF'])->name('productos.pdf');

    Route::put('admin/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('admin/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');


    // Mostrar la lista de ventas
    Route::get('admin/sales', [SalesController::class, 'index'])->name('sales.index');
    Route::get('admin/sales/get-sales', [SalesController::class, 'getSales'])->name('sales.get');
    Route::get('admin/sales/create', [SalesController::class, 'create'])->name('sales-create');
    Route::get('admin/sales/{id}', [SalesController::class, 'show'])->name('sales-show');
    Route::get('admin/sales/{id}/edit', [SalesController::class, 'edit'])->name('sales-edit');
    Route::put('admin/sales/{id}', [SalesController::class, 'update'])->name('sales-update');

    Route::delete('/admin/sales/{id}', [SalesController::class, 'destroy'])->name('sales-destroy');


    // Obtener detalles de un producto por código de barras
    Route::post('admin/sales/get-product-by-barcode', [SalesController::class, 'getProductByBarcode'])
        ->name('sales.product-by-barcode');

    // Finalizar la venta
    Route::post('admin/sales/finalize', [SalesController::class, 'finalizeSale'])
        ->name('sales.finalize');


    // Rutas para generar tickets
    Route::get('admin/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('admin/get-tickets', [TicketController::class, 'getTickets'])->name('get.tickets');
    Route::get('admin/tickets/generate/{saleId}', [TicketController::class, 'generate'])->name('tickets.generate');
    Route::get('admin/tickets/{id}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('admin/tickets/{id}/download', [TicketController::class, 'download'])->name('tickets.download');
    Route::delete('admin/tickets/{id}', [TicketController::class, 'destroy'])->name('tickets.destroy');

    //rutas para generar códigos de barra
    // Mostrar listado de productos sin código de barras
    Route::get('admin/products-without-barcode', [ProductWithoutBarcodeController::class, 'index'])
        ->name('products_without_barcode.index');

    // Mostrar formulario para crear un nuevo producto
    Route::get('admin/products-without-barcode/create', [ProductWithoutBarcodeController::class, 'create'])
        ->name('products_without_barcode.create');

    // Guardar un nuevo producto en la base de datos
    Route::post('admin/products-without-barcode', [ProductWithoutBarcodeController::class, 'store'])
        ->name('products_without_barcode.store');

    // Mostrar formulario para editar un producto existente
    Route::get('admin/products-without-barcode/{id}/edit', [ProductWithoutBarcodeController::class, 'edit'])
        ->name('products_without_barcode.edit');

    // Actualizar un producto existente en la base de datos
    Route::put('admin/products-without-barcode/{id}', [ProductWithoutBarcodeController::class, 'update'])
        ->name('products_without_barcode.update');

    // Eliminar un producto
    Route::delete('admin/products-without-barcode/{id}', [ProductWithoutBarcodeController::class, 'destroy'])
        ->name('products_without_barcode.destroy');

    // Generar código de barras para un producto específico
    Route::get('products_without_barcode/{id}/generate_barcode', [ProductWithoutBarcodeController::class, 'generateBarcode'])
        ->name('products_without_barcode.generateBarcode');

    // Generar un PDF con los productos sin código de barras
    Route::get('admin/products/pdf', [ProductWithoutBarcodeController::class, 'generatePdf'])
        ->name('products_without_barcode.pdf');


    /****************************************** */
    // Ruta para la caja
    Route::get('admin/cashbox', [CashboxController::class, 'index'])->name('cashbox-index');
    Route::post('/admin/caja/pdf/{date}', [CashboxController::class, 'generatePDF'])->name('cashbox.generatePDF');
    Route::get('/cashbox/transactions', [CashboxController::class, 'fetchTransactions'])->name('cashbox.fetch');


    /******************************************** */
    //Rutas para los gastos
    Route::get('/api/expenses', [ExpensesController::class, 'getExpenses'])->name('expenses.getExpenses');
    Route::get('admin/expenses', [ExpensesController::class, 'index'])->name('admin.expenses.index');        // Listar los egresos
    Route::get('admin/expenses/create', [ExpensesController::class, 'create'])->name('admin.expenses.create'); // Mostrar formulario para crear un egreso
    Route::post('admin/expenses', [ExpensesController::class, 'store'])->name('admin.expenses.store');         // Guardar un nuevo egreso
    Route::get('admin/expenses/{expense}', [ExpensesController::class, 'show'])->name('admin.expenses.show');   // Mostrar un egreso específico
    Route::get('admin/expenses/{expense}/edit', [ExpensesController::class, 'edit'])->name('admin.expenses.edit'); // Mostrar formulario para editar un egreso
    Route::put('admin/expenses/{expense}', [ExpensesController::class, 'update'])->name('admin.expenses.update');  // Actualizar un egreso
    Route::delete('admin/expenses/{expense}', [ExpensesController::class, 'destroy'])->name('admin.expenses.destroy'); // Eliminar un egreso
});

require __DIR__ . '/auth.php';
