<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaController;

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

    // CRUD SISWA
    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa.index');
    Route::get('/siswa/{id}/edit', [SiswaController::class, 'edit'])->name('siswa.edit');
    Route::put('/siswa/{id}', [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{id}', [SiswaController::class, 'destroy'])->name('siswa.destroy');

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