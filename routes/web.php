<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetTransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
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
    Route::resource('assets', AssetController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::resource('transactions', AssetTransactionController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::post('exports/assets', [ExportController::class, 'storeAssets'])->name('exports.assets.store');
    Route::post('imports/assets', [ImportController::class, 'assets'])->name('imports.assets.store');
});

Route::fallback(function () {
    return Inertia::render('errors/NotFound');
});
