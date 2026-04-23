<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TahunAjarController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\GuruMapelController;
use App\Http\Controllers\AnggotaKelasController;
use App\Http\Controllers\AssessmentCategoryController;
use App\Http\Controllers\PointRuleController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $siswa = \App\Models\User::where('role', 'murid')->get();
    $kelas = \App\Models\Kelas::all();
    return view('pages.home', compact('siswa', 'kelas'));
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard']);

    // CRUD AKUN ADMIN (superadmin only)
    Route::get('/accounts',             [AdminController::class, 'adminIndex'])->name('admin.accounts.index');
    Route::post('/accounts',            [AdminController::class, 'adminStore'])->name('admin.accounts.store');
    Route::get('/accounts/{id}/edit',   [AdminController::class, 'adminEdit'])->name('admin.accounts.edit');
    Route::put('/accounts/{id}',        [AdminController::class, 'adminUpdate'])->name('admin.accounts.update');
    Route::delete('/accounts/{id}',     [AdminController::class, 'adminDestroy'])->name('admin.accounts.destroy');
    // CRUD TAHUN AJAR
    Route::get('/tahun-ajar',                [TahunAjarController::class, 'index'])->name('tahun-ajar.index');
    Route::post('/tahun-ajar',               [TahunAjarController::class, 'store'])->name('tahun-ajar.store');
    Route::put('/tahun-ajar/{id}',           [TahunAjarController::class, 'update'])->name('tahun-ajar.update');
    Route::delete('/tahun-ajar/{id}',        [TahunAjarController::class, 'destroy'])->name('tahun-ajar.destroy');
    Route::post('/tahun-ajar/{id}/aktifkan', [TahunAjarController::class, 'aktifkan'])->name('tahun-ajar.aktifkan');

    // CRUD MATA PELAJARAN
    Route::get('/mapel',           [MapelController::class, 'index'])->name('mapel.index');
    Route::post('/mapel',          [MapelController::class, 'store'])->name('mapel.store');
    Route::put('/mapel/{id}',      [MapelController::class, 'update'])->name('mapel.update');
    Route::delete('/mapel/{id}',   [MapelController::class, 'destroy'])->name('mapel.destroy');

    // CRUD KELAS
    Route::get('/kelas',           [KelasController::class, 'index'])->name('kelas.index');
    Route::post('/kelas',          [KelasController::class, 'store'])->name('kelas.store');
    Route::put('/kelas/{id}',      [KelasController::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{id}',   [KelasController::class, 'destroy'])->name('kelas.destroy');

    // CRUD JADWAL PELAJARAN
    Route::get('/jadwal',           [JadwalController::class, 'index'])->name('jadwal.index');
    Route::post('/jadwal',          [JadwalController::class, 'store'])->name('jadwal.store');
    Route::put('/jadwal/{id}',      [JadwalController::class, 'update'])->name('jadwal.update');
    Route::delete('/jadwal/{id}',   [JadwalController::class, 'destroy'])->name('jadwal.destroy');

    // CRUD GURU
    Route::get('/guru',             [GuruController::class, 'adminIndex'])->name('guru.admin.index');
    Route::post('/guru',            [GuruController::class, 'adminStore'])->name('guru.admin.store');
    Route::get('/guru/{id}/edit',   [GuruController::class, 'adminEdit'])->name('guru.admin.edit');
    Route::put('/guru/{id}',        [GuruController::class, 'adminUpdate'])->name('guru.admin.update');
    Route::delete('/guru/{id}',     [GuruController::class, 'adminDestroy'])->name('guru.admin.destroy');
    Route::get('/guru/export',      [GuruController::class, 'export'])->name('guru.export');

    // GURU MAPEL (penugasan guru ke mata pelajaran)
    Route::get('/guru-mapel',          [GuruMapelController::class, 'index'])->name('guru-mapel.index');
    Route::post('/guru-mapel',         [GuruMapelController::class, 'store'])->name('guru-mapel.store');
    Route::delete('/guru-mapel',       [GuruMapelController::class, 'destroy'])->name('guru-mapel.destroy');
    Route::get('/guru-mapel/export',   [GuruMapelController::class, 'export'])->name('guru-mapel.export');

    // CRUD SISWA
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
    Route::get('/siswa/export', [SiswaController::class, 'export'])->name('siswa.export');
    Route::get('/siswa/{id}/edit', [SiswaController::class, 'edit'])->name('siswa.edit');
    Route::put('/siswa/{id}', [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{id}', [SiswaController::class, 'destroy'])->name('siswa.destroy');

    // CRUD ANGGOTA KELAS
    Route::get('/anggota-kelas', [AnggotaKelasController::class, 'index'])->name('anggota-kelas.index');
    Route::post('/anggota-kelas', [AnggotaKelasController::class, 'store'])->name('anggota-kelas.store');
    Route::get('/anggota-kelas/export', [AnggotaKelasController::class, 'export'])->name('anggota-kelas.export');
    Route::put('/anggota-kelas/{id}', [AnggotaKelasController::class, 'move'])->name('anggota-kelas.move');
    Route::delete('/anggota-kelas/{id}', [AnggotaKelasController::class, 'destroy'])->name('anggota-kelas.destroy');

    // REKAP & DETAIL ABSENSI
    Route::get('/absensi/rekap',          [AbsensiController::class, 'rekap'])->name('absensi.rekap');
    Route::get('/absensi/rekap/export',   [AbsensiController::class, 'exportRekap'])->name('absensi.rekap.export');
    Route::get('/absensi/sesi/{id}',      [AbsensiController::class, 'detail'])->name('absensi.detail');

    // CRUD ASSESSMENT CATEGORIES
    Route::get('/assessment-categories',              [AssessmentCategoryController::class, 'index'])->name('assessment-categories.index');
    Route::post('/assessment-categories',             [AssessmentCategoryController::class, 'store'])->name('assessment-categories.store');
    Route::put('/assessment-categories/{id}',         [AssessmentCategoryController::class, 'update'])->name('assessment-categories.update');
    Route::post('/assessment-categories/{id}/toggle', [AssessmentCategoryController::class, 'toggleActive'])->name('assessment-categories.toggle');
    Route::delete('/assessment-categories/{id}',      [AssessmentCategoryController::class, 'destroy'])->name('assessment-categories.destroy');
    Route::get('/assessment-categories/export',       [AssessmentCategoryController::class, 'export'])->name('assessment-categories.export');

    // CRUD POINT RULES & FLEXIBILITY ITEMS
    Route::get('point-rules/data', [PointRuleController::class, 'data'])->name('point-rules.data');
    Route::resource('point-rules', PointRuleController::class);

    Route::get('flexibility-items/data', [\App\Http\Controllers\FlexibilityItemController::class, 'data'])->name('flexibility-items.data');
    Route::resource('flexibility-items', \App\Http\Controllers\FlexibilityItemController::class);
});

/*
|--------------------------------------------------------------------------
| GURU
|--------------------------------------------------------------------------
*/
Route::prefix('guru')->group(function () {

    Route::get('/dashboard', [GuruController::class, 'dashboard']);
    Route::get('/profile', [GuruController::class, 'profile']);
    Route::get('/qr', [GuruController::class, 'qr']);
    Route::post('/qr/close', [GuruController::class, 'closeSesi']);

});

/*
|--------------------------------------------------------------------------
| SISWA
|--------------------------------------------------------------------------
*/
Route::prefix('siswa')->group(function () {

    Route::get('/dashboard', [SiswaController::class, 'dashboard']);
    Route::get('/scan', [SiswaController::class, 'scan']);
    Route::get('/history', [SiswaController::class, 'history']);
    Route::get('/subjects', [SiswaController::class, 'subjects']);
    Route::get('/schedule', [SiswaController::class, 'schedule']);

});