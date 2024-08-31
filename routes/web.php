<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
});

require __DIR__.'/auth.php';
