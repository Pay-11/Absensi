<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);

// route yang butuh login
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/sesi-absen', [ApiController::class, 'bukaSesi']);
    Route::post('/scan', [ApiController::class, 'scan']);

    // Tambahan untuk guru
    Route::post('/absen-manual', [ApiController::class, 'absenManual']);
    Route::get('/murid-sesi/{sesiId}', [ApiController::class, 'getMuridSesi']);
});