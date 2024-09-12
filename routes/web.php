<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalesController;
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

    // Otras rutas relacionadas con productos, como crear, editar, eliminar, etc.
    Route::get('admin/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('admin/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('admin/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('admin/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('admin/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Mostrar la lista de ventas
    Route::get('admin/sales', [SalesController::class, 'index'])->name('sales.index');
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
});

require __DIR__ . '/auth.php';
