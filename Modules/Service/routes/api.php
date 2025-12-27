<?php

use Illuminate\Support\Facades\Route;
use Modules\Service\Http\Controllers\ServiceCategoryController;
use Modules\Service\Http\Controllers\ServiceController;
use Modules\Service\Http\Controllers\VenueController;
use Modules\Service\Http\Controllers\PackageController;
use Modules\Service\Http\Controllers\ReviewController;

/*
|--------------------------------------------------------------------------
| API Routes - Service Module
|--------------------------------------------------------------------------
*/

// Public routes (no authentication required)
Route::get('service-categories', [ServiceCategoryController::class, 'index']);
Route::get('service-categories/{id}', [ServiceCategoryController::class, 'show']);

Route::get('services', [ServiceController::class, 'index']);
Route::get('services/{id}', [ServiceController::class, 'show']);

Route::get('venues', [VenueController::class, 'index']);
Route::get('venues/{id}', [VenueController::class, 'show']);

Route::get('packages', [PackageController::class, 'index']);
Route::get('packages/{id}', [PackageController::class, 'show']);

Route::get('reviews', [ReviewController::class, 'index']);

// Protected routes (authentication required)
Route::middleware(['auth:sanctum'])->group(function () {

    // Service Categories (Admin only - add middleware later)
    Route::middleware(['role:admin'])->group(function () {
        Route::post('service-categories', [ServiceCategoryController::class, 'store']);
        Route::put('service-categories/{id}', [ServiceCategoryController::class, 'update']);
        Route::delete('service-categories/{id}', [ServiceCategoryController::class, 'destroy']);
    });

    Route::middleware(['role:provider'])->group(function () {

        // Services (Provider)
        Route::post('services', [ServiceController::class, 'store']);
        Route::put('services/{id}', [ServiceController::class, 'update']);
        Route::delete('services/{id}', [ServiceController::class, 'destroy']);
        Route::get('my-services', [ServiceController::class, 'myServices']);

        // Packages (Provider)
        Route::post('packages', [PackageController::class, 'store']);
        Route::put('packages/{id}', [PackageController::class, 'update']);
        Route::delete('packages/{id}', [PackageController::class, 'destroy']);
        Route::post('packages/{id}/items', [PackageController::class, 'addItem']);
        Route::delete('packages/{packageId}/items/{itemId}', [PackageController::class, 'removeItem']);
    });

    Route::middleware('role:customer')->group(function () {
        // Reviews (Customer)
        Route::post('reviews', [ReviewController::class, 'store']);
        Route::put('reviews/{id}', [ReviewController::class, 'update']);
        Route::delete('reviews/{id}', [ReviewController::class, 'destroy']);
    });
});
