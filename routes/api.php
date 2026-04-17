<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\PenilaianSikapController;
use App\Http\Controllers\Api\QrSessionController;
use App\Http\Controllers\GuruController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ============================
// AUTH (PUBLIC)
// ============================
Route::post('/login', [AuthController::class, 'login']);


// ============================
// ROUTE YANG BUTUH LOGIN
// ============================
Route::middleware('auth:api')->group(function () {

    // ============================
    // AUTH
    // ============================
    Route::post('/logout', [AuthController::class, 'logout']);


    // ============================
    // ABSENSI QR
    // ============================
    Route::prefix('absensi')->group(function () {

        // 🔥 FIX: method name disamain
        Route::post('/sesi', [ApiController::class, 'bukaAbsen']);

        Route::post('/scan', [ApiController::class, 'scan']);

        // ABSEN MANUAL
        Route::post('/manual', [ApiController::class, 'absenManual']);

        Route::get('/murid-sesi/{sesiId}', [ApiController::class, 'getMuridSesi']);
    });


    // ============================
    // DATA AKADEMIK
    // ============================
    Route::prefix('akademik')->group(function () {

        Route::get('/tahun-ajar', [ApiController::class, 'tahunAjar']);
        Route::post('/kelas', [ApiController::class, 'kelas']);
        Route::get('/murid-kelas/{kelasId}', [ApiController::class, 'muridKelas']);
    });


    // ============================
    // ASSESSMENT / PENILAIAN
    // ============================
    Route::prefix('assessment')->group(function () {

        Route::get('/categories', [ApiController::class, 'assessmentCategories']);
        Route::post('/', [ApiController::class, 'simpanAssessment']);
        Route::get('/murid/{muridId}', [ApiController::class, 'nilaiMurid']);
    });


    // ============================
    // PENILAIAN SIKAP
    // ============================
    Route::post('/penilaian-sikap', [PenilaianSikapController::class, 'store']);


    // ============================
    // JADWAL
    // ============================
    Route::prefix('jadwal')->group(function () {

        Route::get('/guru-hari-ini', [ApiController::class, 'jadwalHariIni']);
        Route::get('/murid-hari-ini', [ApiController::class, 'jadwalMuridHariIni']);
        Route::get('/murid-mingguan', [ApiController::class, 'jadwalMingguMurid']);
    });


    // ============================
    // POINT & TOKEN
    // ============================
    Route::prefix('point')->group(function () {

        Route::get('/my', [ApiController::class, 'myPoint']);
        Route::get('/history', [ApiController::class, 'pointHistory']);
        Route::get('/leaderboard', [ApiController::class, 'leaderboard']);
    });

    Route::prefix('token')->group(function () {

        Route::get('/items', [ApiController::class, 'getItems']);
        Route::post('/buy', [ApiController::class, 'buyToken']);
        Route::get('/my', [ApiController::class, 'myTokens']);
    });


    // ============================
    // QR GURU (FIX: MASUK AUTH)
    // ============================
    Route::post('/guru/qr/refresh', [GuruController::class, 'refreshQr']);
});


// ============================
// QR GENERATOR (PUBLIC - OPTIONAL)
// ============================
// ⚠️ kalau ini penting, mending kasih auth juga
// Route::get('/generate-qr', [QrSessionController::class, 'generate']);