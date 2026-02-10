<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StockMovementController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\Api\ReportController;

// Auth
Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Categories (write: editor, admin)
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::middleware('role:editor,admin')->group(function () {
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    });

    // Products (write: editor, admin)
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::middleware('role:editor,admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    });

    // Stock movements (write: editor, admin)
    Route::middleware('role:editor,admin')->post('/stock-movements', [StockMovementController::class, 'store']);
    Route::get('/stock-movements', [StockMovementController::class, 'index']);
    Route::get('/stock-movements/product/{product}', [StockMovementController::class, 'byProduct']);

    // Reports
    Route::get('/reports/out-of-stock', [ReportController::class, 'outOfStock']);
    Route::get('/reports/low-stock', [ReportController::class, 'lowStock']);
    Route::get('/reports/movement-summary', [ReportController::class, 'movementSummary']);
});

// Sync API (for PHP site) - optional API key via middleware
Route::middleware('sync.api.key')->prefix('sync')->group(function () {
    Route::get('/products', [SyncController::class, 'products']);
    Route::get('/products/slug/{slug}', [SyncController::class, 'productBySlug']);
    Route::get('/products/{product}', [SyncController::class, 'product']);
    Route::get('/categories', [SyncController::class, 'categories']);
});
