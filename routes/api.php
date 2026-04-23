<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\PenilaianSikapController;
use App\Http\Controllers\Api\QrSessionController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\GuruController as ApiGuruController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\TahunAjarController as ApiTahunAjarController;
use App\Http\Controllers\Api\MapelController as ApiMapelController;
use App\Http\Controllers\Api\JadwalController as ApiJadwalController;
use App\Http\Controllers\Api\KelasController as ApiKelasController;
use App\Http\Controllers\Api\RekapAbsensiController;
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
    Route::get('/me', [ApiController::class, 'me']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);


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
        Route::get('/riwayat', [ApiController::class, 'riwayatAbsensi']);
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
    // CRUD SISWA (MURID)
    // ============================
    Route::prefix('siswa')->group(function () {
        Route::get('/',         [SiswaController::class, 'index']);   // GET    /api/siswa
        Route::post('/',        [SiswaController::class, 'store']);   // POST   /api/siswa
        Route::get('/{id}',     [SiswaController::class, 'show']);    // GET    /api/siswa/{id}
        Route::put('/{id}',     [SiswaController::class, 'update']);  // PUT    /api/siswa/{id}
        Route::delete('/{id}',  [SiswaController::class, 'destroy']); // DELETE /api/siswa/{id}
    });


    // ============================
    // CRUD AKUN ADMIN (superadmin only)
    // ============================
    Route::prefix('admins')->group(function () {
        Route::get('/',        [AdminUserController::class, 'index']);   // GET    /api/admins
        Route::post('/',       [AdminUserController::class, 'store']);   // POST   /api/admins
        Route::get('/{id}',    [AdminUserController::class, 'show']);    // GET    /api/admins/{id}
        Route::put('/{id}',    [AdminUserController::class, 'update']);  // PUT    /api/admins/{id}
        Route::delete('/{id}', [AdminUserController::class, 'destroy']); // DELETE /api/admins/{id}
    });


    // ============================
    // CRUD GURU
    // ============================
    Route::prefix('guru')->group(function () {
        Route::get('/',         [ApiGuruController::class, 'index']);   // GET    /api/guru
        Route::post('/',        [ApiGuruController::class, 'store']);   // POST   /api/guru
        Route::get('/{id}',     [ApiGuruController::class, 'show']);    // GET    /api/guru/{id}
        Route::put('/{id}',     [ApiGuruController::class, 'update']);  // PUT    /api/guru/{id}
        Route::delete('/{id}',  [ApiGuruController::class, 'destroy']); // DELETE /api/guru/{id}
    });


    // ============================
    // CRUD TAHUN AJAR
    // ============================
    Route::prefix('tahun-ajar')->group(function () {
        Route::get('/',              [ApiTahunAjarController::class, 'index']);    // GET    /api/tahun-ajar
        Route::post('/',             [ApiTahunAjarController::class, 'store']);    // POST   /api/tahun-ajar
        Route::get('/{id}',          [ApiTahunAjarController::class, 'show']);     // GET    /api/tahun-ajar/{id}
        Route::put('/{id}',          [ApiTahunAjarController::class, 'update']);   // PUT    /api/tahun-ajar/{id}
        Route::delete('/{id}',       [ApiTahunAjarController::class, 'destroy']);  // DELETE /api/tahun-ajar/{id}
        Route::post('/{id}/aktifkan',[ApiTahunAjarController::class, 'aktifkan']); // POST   /api/tahun-ajar/{id}/aktifkan
    });


    // ============================
    // CRUD MATA PELAJARAN
    // ============================
    Route::prefix('mapel')->group(function () {
        Route::get('/',        [ApiMapelController::class, 'index']);   // GET    /api/mapel
        Route::post('/',       [ApiMapelController::class, 'store']);   // POST   /api/mapel
        Route::get('/{id}',    [ApiMapelController::class, 'show']);    // GET    /api/mapel/{id}
        Route::put('/{id}',    [ApiMapelController::class, 'update']);  // PUT    /api/mapel/{id}
        Route::delete('/{id}', [ApiMapelController::class, 'destroy']); // DELETE /api/mapel/{id}
    });


    // ============================
    // CRUD JADWAL PELAJARAN
    // ============================
    Route::prefix('jadwal')->group(function () {
        Route::get('/',        [ApiJadwalController::class, 'index']);   // GET    /api/jadwal
        Route::post('/',       [ApiJadwalController::class, 'store']);   // POST   /api/jadwal
        Route::get('/{id}',    [ApiJadwalController::class, 'show']);    // GET    /api/jadwal/{id}
        Route::put('/{id}',    [ApiJadwalController::class, 'update']);  // PUT    /api/jadwal/{id}
        Route::delete('/{id}', [ApiJadwalController::class, 'destroy']); // DELETE /api/jadwal/{id}
    });


    // ============================
    // CRUD KELAS
    // ============================
    Route::prefix('kelas')->group(function () {
        Route::get('/',        [ApiKelasController::class, 'index']);   // GET    /api/kelas
        Route::post('/',       [ApiKelasController::class, 'store']);   // POST   /api/kelas
        Route::get('/{id}',    [ApiKelasController::class, 'show']);    // GET    /api/kelas/{id}
        Route::put('/{id}',    [ApiKelasController::class, 'update']);  // PUT    /api/kelas/{id}
        Route::delete('/{id}', [ApiKelasController::class, 'destroy']); // DELETE /api/kelas/{id}
    });


    // ============================
    // REKAP ABSENSI
    // ============================
    Route::get('/absensi/rekap',    [RekapAbsensiController::class, 'rekap']);      // GET /api/absensi/rekap
    Route::get('/absensi/sesi/{id}',[RekapAbsensiController::class, 'detailSesi']); // GET /api/absensi/sesi/{id}


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