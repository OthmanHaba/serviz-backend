<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Api\ProviderController;
use App\Http\Controllers\api\ServicController;
use App\Http\Controllers\Api\ServiceController;
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

    Route::prefix('provider')->group(function () {
        Route::get('/active-providers', [ProviderController::class, 'activeProviders']);
        Route::post('/toggle-active', [ProviderController::class, 'toggleActive']);
        Route::post('/update-location', [ProviderController::class, 'updateLocation']);
    });

    Route::prefix('service')->group(function () {
        Route::post('lockup-service', [ServiceController::class, 'lockUp']);
        Route::post('user/conform-service',[ServiceController::class,'userApproveRequest']);
    });

    Route::post('/expo-token', [AuthController::class, 'storeExpoToken']);
});

// Route::get('/reverb/auth', function () {
//     $client = new \Reverb\Client(env('REVERB_APP_ID'), env('REVERB_APP_KEY'), env('REVERB_APP_SECRET'));
//     $authUrl = $client->getAuthorizationUrl();
//     return redirect($authUrl);
// });
