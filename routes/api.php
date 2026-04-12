<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\PenilaianSikapController;
use App\Http\Controllers\Api\QrSessionController;
use App\Http\Controllers\GuruController;

/* |-------------------------------------------------------------------------- | API Routes |-------------------------------------------------------------------------- */

// ============================
// AUTH
// ============================
Route::post('/login', [AuthController::class , 'login']);


// ============================
// ROUTE YANG BUTUH LOGIN
// ============================
Route::middleware('auth:api')->group(function () {

    // AUTH
    Route::post('/logout', [AuthController::class , 'logout']);

    // ============================
    // ABSENSI QR
    // ============================
    Route::post('/sesi-absen', [ApiController::class , 'bukaSesi']);
    Route::post('/scan', [ApiController::class , 'scan']);

    // ABSEN MANUAL GURU
    Route::post('/absen-manual', [ApiController::class , 'absenManual']);
    Route::get('/murid-sesi/{sesiId}', [ApiController::class , 'getMuridSesi']);


    // ============================
    // DATA AKADEMIK
    // ============================
    Route::get('/tahun-ajar', [ApiController::class , 'tahunAjar']);
    Route::post('/kelas', [ApiController::class , 'kelas']);
    Route::get('/murid-kelas/{kelasId}', [ApiController::class , 'muridKelas']);


    // ============================
    // ASSESSMENT / PENILAIAN
    // ============================
    Route::get('/assessment-categories', [ApiController::class , 'assessmentCategories']);
    Route::post('/assessment', [ApiController::class , 'simpanAssessment']);
    Route::get('/nilai-murid/{muridId}', [ApiController::class , 'nilaiMurid']);


    // ============================
    // PENILAIAN SIKAP
    // ============================
    Route::post('/penilaian-sikap', [PenilaianSikapController::class , 'store']);

});


// ============================
// QR GENERATOR (PUBLIC)
// ============================
Route::get('/generate-qr', [QrSessionController::class , 'generate']);


// ============================
// REFRESH QR GURU
// ============================
Route::post('/guru/qr/refresh', [GuruController::class , 'refreshQr']);