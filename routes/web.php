<?php

use App\Http\Controllers\ProfileController;
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

    //usuarios
    Route::get('/admin/users', [UserController::class, 'index'])->name('users');
    Route::delete('/admin/users/destroy/{id}', [UserController::class, 'destroy'])->name('users-destroy');
    Route::get('/admin/users/{user}/edit-role', [UserController::class, 'editRole'])->name('users-edit-role');
    Route::put('/admin/users/{user}/update-role', [UserController::class, 'updateRole'])->name('users-update-role');
});

require __DIR__.'/auth.php';
