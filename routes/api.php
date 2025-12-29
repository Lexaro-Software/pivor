<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\CommunicationController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\TokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Token management (public)
Route::post('/tokens/create', [TokenController::class, 'store']);
Route::middleware('auth:sanctum')->delete('/tokens/revoke', [TokenController::class, 'destroy']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Current user
    Route::get('/user', fn(Request $request) => $request->user());

    // Clients
    Route::apiResource('clients', ClientController::class);

    // Contacts
    Route::apiResource('contacts', ContactController::class);

    // Communications
    Route::apiResource('communications', CommunicationController::class);
});
