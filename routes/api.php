<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('auth')->group(function () {
  Route::post('/register', [AuthController::class, 'register']);
  Route::post('/login', [AuthController::class, 'login']);
});

// Public property routes (read-only for guests)
Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{id}', [PropertyController::class, 'show']);

// Protected routes (requires authentication)
Route::middleware('auth:sanctum')->group(function () {

  // Auth routes
  Route::prefix('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
  });

  // Property routes
  Route::prefix('properties')->group(function () {
    Route::post('/', [PropertyController::class, 'store']);
    Route::put('/{id}', [PropertyController::class, 'update']);
    Route::delete('/{id}', [PropertyController::class, 'destroy']);
    Route::post('/{id}/toggle-publish', [PropertyController::class, 'togglePublish']);
  });

  // Image routes
  Route::prefix('images')->group(function () {
    Route::post('/properties/{propertyId}', [ImageController::class, 'upload']);
    Route::delete('/{id}', [ImageController::class, 'destroy']);
    Route::post('/{id}/set-primary', [ImageController::class, 'setPrimary']);
  });
});
