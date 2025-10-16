<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DemografiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\MapController;

Route::get('/peta-wilayah', [MapController::class, 'index'])->name('peta.index');
Route::get('/api/locations', [MapController::class, 'getLocationsApi'])->name('api.locations');

// --- API UNTUK FORM DINAMIS ---
Route::get('/api/get-rt-by-rw/{rw_id}', [AdminDashboardController::class, 'getRtByRw']);

// --- ROUTE PUBLIK ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/demografi', [DemografiController::class, 'index'])->name('demografi.index');

// --- ROUTE UNTUK AUTENTIKASI ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// --- ROUTE ADMIN GROUP ---
Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // --- FUNGSI CRUD PENDUDUK ---
    Route::get('/penduduk', [AdminDashboardController::class, 'indexPenduduk'])->name('penduduk.index');
    Route::get('/penduduk/tambah', [AdminDashboardController::class, 'createPenduduk'])->name('penduduk.create');
    Route::post('/penduduk', [AdminDashboardController::class, 'storePenduduk'])->name('penduduk.store');
    Route::get('/penduduk/{penduduk}/edit', [AdminDashboardController::class, 'editPenduduk'])->name('penduduk.edit');
    Route::put('/penduduk/{penduduk}', [AdminDashboardController::class, 'updatePenduduk'])->name('penduduk.update');
    Route::delete('/penduduk/{penduduk}', [AdminDashboardController::class, 'destroyPenduduk'])->name('penduduk.destroy');

    // --- FUNGSI CRUD RT/RW ---
    Route::get('/rt-rw', [AdminDashboardController::class, 'indexRtRw'])->name('rt-rw.index');
    Route::get('/rt-rw/create', [AdminDashboardController::class, 'createRtRw'])->name('rt-rw.create');
    Route::post('/rt-rw', [AdminDashboardController::class, 'storeRtRw'])->name('rt-rw.store');
    Route::get('/rt-rw/{type}/{id}/edit', [AdminDashboardController::class, 'editRtRw'])->name('rt-rw.edit');
    Route::put('/rt-rw/{type}/{id}', [AdminDashboardController::class, 'updateRtRw'])->name('rt-rw.update');
    Route::delete('/rt-rw/{type}/{id}', [AdminDashboardController::class, 'destroyRtRw'])->name('rt-rw.destroy');

    // --- FUNGSI CRUD KOMODITAS ---
    Route::get('/komoditas', [AdminDashboardController::class, 'indexKomoditas'])->name('komoditas.index');
    Route::get('/komoditas/tambah', [AdminDashboardController::class, 'createKomoditas'])->name('komoditas.create');
    Route::post('/komoditas', [AdminDashboardController::class, 'storeKomoditas'])->name('komoditas.store');
    Route::get('/komoditas/{komoditas}/edit', [AdminDashboardController::class, 'editKomoditas'])->name('komoditas.edit');
    Route::put('/komoditas/{komoditas}', [AdminDashboardController::class, 'updateKomoditas'])->name('komoditas.update');
    Route::delete('/komoditas/{komoditas}', [AdminDashboardController::class, 'destroyKomoditas'])->name('komoditas.destroy');

    // --- FUNGSI CRUD BANGUNAN ---
    Route::get('/bangunan', [AdminDashboardController::class, 'indexBangunan'])->name('bangunan.index');
    Route::get('/bangunan/create', [AdminDashboardController::class, 'createBangunan'])->name('bangunan.create');
    Route::post('/bangunan/store', [AdminDashboardController::class, 'storeBangunan'])->name('bangunan.store');
    Route::get('/bangunan/{bangunan}/edit', [AdminDashboardController::class, 'editBangunan'])->name('bangunan.edit');
    Route::put('/bangunan/{bangunan}', [AdminDashboardController::class, 'updateBangunan'])->name('bangunan.update');
    Route::delete('/bangunan/{bangunan}', [AdminDashboardController::class, 'destroyBangunan'])->name('bangunan.destroy');

});