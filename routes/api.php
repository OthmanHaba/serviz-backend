<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ServiceProviderController;
use App\Http\Controllers\API\ServiceRequestController;
use App\Http\Controllers\API\PaymentController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);

    // Service Provider routes
    Route::prefix('providers')->group(function () {
        Route::get('/nearby', [ServiceProviderController::class, 'findNearby']);
        Route::get('/{provider}', [ServiceProviderController::class, 'show']);
        Route::post('/', [ServiceProviderController::class, 'store']);
        Route::patch('/{provider}/availability', [ServiceProviderController::class, 'updateAvailability']);
        Route::post('/{provider}/location', [ServiceProviderController::class, 'updateLocation']);
    });

    // Service Request routes
    Route::prefix('requests')->group(function () {
        Route::get('/user', [ServiceRequestController::class, 'userRequests']);
        Route::get('/provider', [ServiceRequestController::class, 'providerRequests']);
        Route::get('/{serviceRequest}', [ServiceRequestController::class, 'show']);
        Route::post('/', [ServiceRequestController::class, 'store']);
        Route::post('/{serviceRequest}/accept', [ServiceRequestController::class, 'acceptRequest']);
        Route::patch('/{serviceRequest}/status', [ServiceRequestController::class, 'updateStatus']);
    });

    // Payment routes
    Route::prefix('payments')->group(function () {
        Route::post('/requests/{serviceRequest}', [PaymentController::class, 'createPayment']);
        Route::post('/{payment}/process', [PaymentController::class, 'processPayment']);
        Route::get('/{transactionId}/status', [PaymentController::class, 'getStatus']);
    });
});
