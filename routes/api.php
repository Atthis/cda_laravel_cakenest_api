<?php

use App\Http\Controllers\CupcakeController;
use App\Http\Controllers\PurchaseController;
use App\Http\Middleware\AdminRoutesChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes
Route::get('/cupcakes', [CupcakeController::class, 'index'])->name('cupcake.all');
Route::get('/cupcake/{id}', [CupcakeController::class, 'show'])->name('cupcake.show');

// Authenticated routes
Route::middleware(['auth:sanctum'])->group(function() {
    // Purchase routes
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchase.all');
    Route::get('/purchase/{id}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase', [PurchaseController::class, 'store'])->name('purchase.create');

    // Admin routes
    Route::middleware(AdminRoutesChecker::class)->group(function() {
        Route::post('/cupcake', [CupcakeController::class, 'store'])->name('cupcake.create');
        Route::patch('/cupcake/{id}', [CupcakeController::class, 'update'])->name('cupcake.update');
        Route::delete('/cupcake/{id}', [CupcakeController::class, 'destroy'])->name('cupcake.delete');
    });
});
