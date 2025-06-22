<?php
// routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\AnalyticsController;
use App\Http\Controllers\API\CategoryController;

Route::middleware('throttle:60,1')->group(function () {

    // Rutas de Autenticación
    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('login', [AuthController::class, 'login'])->name('login');
        
        Route::middleware('auth:api')->group(function() {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
            Route::get('me', [AuthController::class, 'me'])->name('me');
        });
    });

    // Rutas de Productos
    Route::group(['prefix' => 'products', 'as' => 'products.'], function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/search/suggestions', [ProductController::class, 'searchSuggestions'])->name('search.suggestions');
        Route::get('/{id}', [ProductController::class, 'show'])->name('show');
        Route::get('/{id}/views', [ProductController::class, 'registerView'])->name('views');
        
        Route::middleware(['auth:api', 'admin'])->group(function() {
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::put('/{id}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/images', [ProductController::class, 'uploadImage'])->name('images.upload');
        });
    });

    // Rutas de Analíticas (Protegidas para Admins)
    Route::group(['prefix' => 'analytics', 'as' => 'analytics.', 'middleware' => ['auth:api', 'admin']], function () {
        Route::get('low-stock', [AnalyticsController::class, 'lowStock'])->name('low-stock');
        Route::get('popular', [AnalyticsController::class, 'popularProducts'])->name('popular');
    });

    // Rutas de Categorías
    Route::apiResource('categories', CategoryController::class);
    // --- NUEVA RUTA AÑADIDA AQUÍ ---
    Route::get('categories/{category}/products', [CategoryController::class, 'products'])->name('categories.products');

});