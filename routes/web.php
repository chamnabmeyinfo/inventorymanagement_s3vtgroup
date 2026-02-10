<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\StockMovementController;
use App\Http\Controllers\Admin\ReportController;

Route::get('/', fn () => auth()->check() ? redirect()->route('admin.dashboard') : redirect()->route('admin.login'));

Route::get('/admin/login', [LoginController::class, 'show'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'login']);

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::resource('products', ProductController::class)->except(['show']);
    Route::post('products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::post('categories/{category}/duplicate', [\App\Http\Controllers\Admin\CategoryController::class, 'duplicate'])->name('categories.duplicate');
    Route::resource('suppliers', \App\Http\Controllers\Admin\SupplierController::class)->except(['show']);
    Route::post('suppliers/{supplier}/duplicate', [\App\Http\Controllers\Admin\SupplierController::class, 'duplicate'])->name('suppliers.duplicate');
    Route::get('/stock-movements', fn () => redirect()->route('admin.stock-movements.create'));
    Route::get('/stock-movements/create', [StockMovementController::class, 'create'])->name('stock-movements.create');
    Route::post('/stock-movements', [StockMovementController::class, 'store'])->name('stock-movements.store');
    Route::get('/stock-movements/product/{product}/history', [StockMovementController::class, 'history'])->name('stock-movements.history');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    Route::middleware('role:admin')->group(function () {
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/test-alert', [\App\Http\Controllers\Admin\SettingController::class, 'testAlert'])->name('settings.test-alert');
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['show']);
    });
});
