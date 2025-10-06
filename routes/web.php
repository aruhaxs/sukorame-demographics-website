<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DemografiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;

// --- ROUTE PUBLIK ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/demografi', [DemografiController::class, 'index'])->name('demografi.index');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// --- ROUTE ADMIN GROUP ---
Route::prefix('admin')->name('admin.')->group(function () {

    // Halaman Dashboard Utama (Hanya Ringkasan)
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // --- HALAMAN UNTUK LIHAT DATA (SESUAI MENU NAVBAR) ---
    Route::get('/penduduk', [AdminDashboardController::class, 'indexPenduduk'])->name('penduduk.index');
    Route::get('/rt-rw', [AdminDashboardController::class, 'indexPerangkat'])->name('perangkat.index');
    Route::get('/komoditas', [AdminDashboardController::class, 'indexKomoditas'])->name('komoditas.index');
    Route::get('/bangunan', [AdminDashboardController::class, 'indexBangunan'])->name('bangunan.index');

    // --- FUNGSI CRUD PENDUDUK ---
    Route::get('/penduduk/tambah', [AdminDashboardController::class, 'createPenduduk'])->name('penduduk.create');
    Route::post('/penduduk', [AdminDashboardController::class, 'storePenduduk'])->name('penduduk.store');
    Route::get('/penduduk/{penduduk}/edit', [AdminDashboardController::class, 'editPenduduk'])->name('penduduk.edit');
    Route::put('/penduduk/{penduduk}', [AdminDashboardController::class, 'updatePenduduk'])->name('penduduk.update');
    Route::delete('/penduduk/{penduduk}', [AdminDashboardController::class, 'destroyPenduduk'])->name('penduduk.destroy');

    // --- FUNGSI CRUD PERANGKAT / RT/RW ---
    Route::get('/rt-rw/tambah', [AdminDashboardController::class, 'createPerangkat'])->name('perangkat.create');
    Route::post('/rt-rw', [AdminDashboardController::class, 'storePerangkat'])->name('perangkat.store');
    Route::get('/rt-rw/{perangkat}/edit', [AdminDashboardController::class, 'editPerangkat'])->name('perangkat.edit');
    Route::put('/rt-rw/{perangkat}', [AdminDashboardController::class, 'updatePerangkat'])->name('perangkat.update');
    Route::delete('/rt-rw/{perangkat}', [AdminDashboardController::class, 'destroyPerangkat'])->name('perangkat.destroy');

// --- FUNGSI CRUD KOMODITAS ---
    Route::get('/komoditas/tambah', [AdminDashboardController::class, 'createKomoditas'])->name('komoditas.create');
    Route::post('/komoditas', [AdminDashboardController::class, 'storeKomoditas'])->name('komoditas.store');
    Route::get('/komoditas/{komoditas}/edit', [AdminDashboardController::class, 'editKomoditas'])->name('komoditas.edit');
    Route::put('/komoditas/{komoditas}', [AdminDashboardController::class, 'updateKomoditas'])->name('komoditas.update');
    Route::delete('/komoditas/{komoditas}', [AdminDashboardController::class, 'destroyKomoditas'])->name('komoditas.destroy');

});
