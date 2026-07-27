<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetTransactionController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'Welcome')->name('home');
Route::inertia('/login', 'auth/Login')->name('login');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/audits', [AuditController::class, 'index'])->name('audits.index');
    Route::resource('roles', RoleController::class)->only(['index', 'store', 'update', 'destroy']);

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
    Route::post('exports/users', [ExportController::class, 'storeUsers'])->name('exports.users.store');
    Route::post('imports/users', [ImportController::class, 'users'])->name('imports.users.store');
    Route::post('exports/categories', [ExportController::class, 'storeCategories'])->name('exports.categories.store');
    Route::post('imports/categories', [ImportController::class, 'categories'])->name('imports.categories.store');
    Route::post('exports/transactions', [ExportController::class, 'storeTransactions'])->name('exports.transactions.store');
    Route::post('imports/transactions', [ImportController::class, 'transactions'])->name('imports.transactions.store');
});

Route::fallback(function () {
    return Inertia::render('errors/NotFound');
});
