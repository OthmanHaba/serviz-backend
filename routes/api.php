<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProviderController;
use App\Http\Controllers\API\ServicController;
use App\Http\Controllers\API\ServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('service')->group(function () {
    Route::get('/', [ServicController::class, 'index']);
    Route::get('/{id}', [ServicController::class, 'show']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', [\App\Http\Controllers\API\UserController::class, 'profile']);

    Route::prefix('provider')->group(function () {

        Route::get('/active-providers', [ProviderController::class, 'activeProviders']);

        Route::post('/toggle-active', [ProviderController::class, 'toggleActive']);

        Route::post('/update-location', [ProviderController::class, 'updateLocation']);

        Route::post('/update-expo-token', function (Request $req) {

            $req->validate([
                'token' => 'string|required',
            ]);

            Auth::user()->expoToken()->firstOrCreate([
                'token' => $req->token,
            ]);
        });

        Route::get('/active-request', [ProviderController::class, 'getActiveRequests']);
    });

    Route::prefix('service')->group(function () {

        Route::post('lockup-service', [ServiceController::class, 'lockUp']);

        Route::post('user/conform-service', [ServiceController::class, 'userApproveRequest']);

        Route::get('track/get-status', [ServiceController::class, 'getStatus'])
            ->name('service.track');

        Route::post('provider/conform-service', [ServiceController::class, 'providerApproveOrDeclineRequest']);

        Route::get('user/refresh-active-request', [\App\Http\Controllers\API\UserController::class, 'refreshActiveRequest']);

        Route::post('active-request/complete', [ProviderController::class, 'completeActiveRequest']);

    });

    Route::prefix('user')->group(function () {
        Route::get('history', [\App\Http\Controllers\API\UserController::class, 'history']);
    });

    Route::post('/expo-token', [AuthController::class, 'storeExpoToken']);

    Route::get('/provider/statistics', [\App\Http\Controllers\API\ProviderController::class, 'todayStatics']);
    Route::post('/provider/add-service', [ProviderController::class, 'addOrSaveService']);

    Route::delete('/provider/services/{serviceId}', function (Request $request) {

        $serviceId = $request->route('serviceId');

        $services = \App\Models\ProviderService::where('servic_type_id', $serviceId)
            ->where('user_id', Auth::id());

        \Illuminate\Support\Facades\Log::log(
            'info',
            'Deleting service with ID: '.$serviceId.' for user ID: '.Auth::id()
        );
        $services->delete();

        return response()->noContent();
    });

});
