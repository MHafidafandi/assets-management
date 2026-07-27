<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'Welcome')->name('Welcome');
Route::inertia('/login', 'auth/Login')->name('login');

Route::middleware(['auth'])->group(function () {
    Route::middleware(['role:Administrator'])->group(function () {
        Route::resource('users', UserController::class)
            ->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::resource('categories', CategoryController::class)
            ->only(['index', 'store', 'show', 'update', 'destroy']);
    });
});

Route::fallback(function () {
    return Inertia::render('errors/NotFound');
});
