<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerisaiController;
use App\Http\Controllers\AuthController;

// Hanya bisa diakses kalau BELUM LOGIN
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
});

// Hanya bisa diakses kalau SUDAH LOGIN
Route::middleware('auth')->group(function () {
    // Menu Utama Form
    Route::get('/', [PerisaiController::class, 'index'])->name('perisai.index');
    Route::post('/', [PerisaiController::class, 'store'])->name('perisai.store');
    
    // Menu Riwayat
    Route::get('/riwayat', [PerisaiController::class, 'riwayat'])->name('perisai.riwayat');

    // TAMBAHAN: Route Cetak dari Riwayat
    Route::get('/riwayat/excel/{id}', [PerisaiController::class, 'exportExcelRiwayat'])->name('perisai.riwayat.excel');
    Route::get('/riwayat/word/{id}', [PerisaiController::class, 'exportWordRiwayat'])->name('perisai.riwayat.word');
    
    // Menu Kelola Kuesioner (CRUD)
    Route::get('/kuesioner', [PerisaiController::class, 'kuesioner'])->name('perisai.kuesioner');
    Route::post('/kuesioner/tambah', [PerisaiController::class, 'storeKuesioner'])->name('perisai.kuesioner.store');
    Route::post('/kuesioner/hapus/{id}', [PerisaiController::class, 'destroyKuesioner'])->name('perisai.kuesioner.destroy');

    // Menu Kelola Tim Penilai
    Route::get('/tim', [App\Http\Controllers\PerisaiController::class, 'tim'])->name('perisai.tim');
    Route::post('/tim', [App\Http\Controllers\PerisaiController::class, 'storeTim'])->name('perisai.tim.store');
    Route::delete('/tim/{id}', [App\Http\Controllers\PerisaiController::class, 'destroyTim'])->name('perisai.tim.destroy');

    // TAMBAHKAN BARIS INI UNTUK FITUR EDIT
    Route::post('/tim/update/{id}', [App\Http\Controllers\PerisaiController::class, 'updateTim'])->name('perisai.tim.update');

    // Route Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::delete('/perisai/riwayat/{id}', [App\Http\Controllers\PerisaiController::class, 'destroyRiwayat'])->name('perisai.riwayat.destroy');
});