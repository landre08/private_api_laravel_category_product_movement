<?php

use App\Http\Controllers\api\v1\AuthController;
use App\Http\Controllers\Api\V1\MainController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function() {

    Route::get('/status', [MainController::class, 'status']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // read
    Route::middleware('ability:read')->group(function() {
        Route::get('/categories', [MainController::class, 'listCategories']);
        Route::get('/products', [MainController::class, 'listProducts']);
        Route::get('/movements', [MainController::class, 'listMovements']);
        Route::get('/categories/{id}', [MainController::class, 'getCategory']);
        Route::get('/products/{id}', [MainController::class, 'getProduct']);
        Route::get('/categories/{id}/products', [MainController::class, 'getProductsByCategory']);
        Route::get('/movements/ordered/{field}/{direction}', [MainController::class, 'listMovementsOrdered']);
    });

    Route::middleware('ability:write')->group(function() {
        Route::post('/categories/create', [MainController::class, 'createCategory']);
        Route::post('/products/create', [MainController::class, 'createProduct']);
        Route::post('/movements/create', [MainController::class, 'createMovement']);
        Route::put('/categories/{id}/update', [MainController::class, 'updateCategory']);
        Route::put('/products/{id}/update', [MainController::class, 'updateProduct']);
        Route::put('/movements/{id}/update', [MainController::class, 'updateMovement']);
    });

    // write
    Route::middleware('ability:delete')->group(function() {
        Route::delete('/movements/{id}/delete', [MainController::class, 'deleteMovement']);
        Route::delete('/products/{id}/delete', [MainController::class, 'deleteProduct']);
        Route::delete('/categories/{id}/delete', [MainController::class, 'deleteCategory']);
    });
});


