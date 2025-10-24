<?php

use Illuminate\Support\Facades\Route;

// --- CONTROLLER IMPORTS ---
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DemografiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\KomoditasPublikController; // <-- DI-AKTIFKAN

// --- ADMIN CONTROLLERS ---
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PendudukController;
use App\Http\Controllers\Admin\RtRwController;
use App\Http\Controllers\Admin\KomoditasController;
use App\Http\Controllers\Admin\BangunanController;

// --- API CONTROLLERS ---
use App\Http\Controllers\Api\WilayahController;
use App\Http\Controllers\Api\BangunanMapController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- ROUTE PUBLIK ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/demografi', [DemografiController::class, 'index'])->name('demografi.index');

// Rute Peta Publik (menggunakan MapController)
Route::get('/peta-sebaran', [MapController::class, 'index'])->name('peta.publik');

// Rute Komoditas Publik
Route::get('/komoditas', [KomoditasPublikController::class, 'index'])->name('komoditas.publik'); // <-- DI-AKTIFKAN

// Catatan: Route '/peta-wilayah' lama masih ada jika Anda masih menggunakannya
Route::get('/peta-wilayah', [MapController::class, 'index'])->name('peta.index');


// --- ROUTE UNTUK AUTENTIKASI ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// --- ROUTE ADMIN GROUP (TANPA MIDDLEWARE) ---
Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

    // --- DASHBOARD ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- CRUD PENDUDUK (Menggunakan Route Resource) ---
    Route::resource('penduduk', PendudukController::class);

    // --- CRUD KOMODITAS (Menggunakan Route Resource) ---
    Route::resource('komoditas', KomoditasController::class)->except(['show']);

    // --- CRUD BANGUNAN (Menggunakan Route Resource) ---
    Route::resource('bangunan', BangunanController::class)->except(['show']);

    // --- FUNGSI CRUD RT/RW (Didefinisikan manual) ---
    Route::prefix('rt-rw')->name('rt-rw.')->group(function () {
        Route::get('/', [RtRwController::class, 'index'])->name('index');
        Route::get('/create', [RtRwController::class, 'create'])->name('create');
        Route::post('/', [RtRwController::class, 'store'])->name('store');
        Route::get('/{type}/{id}/edit', [RtRwController::class, 'edit'])->name('edit');
        Route::put('/{type}/{id}', [RtRwController::class, 'update'])->name('update');
        Route::delete('/{type}/{id}', [RtRwController::class, 'destroy'])->name('destroy');
    });
});


// --- ROUTE UNTUK API ---
Route::get('/api/get-rt-by-rw/{rw_id}', [WilayahController::class, 'getRtByRw'])->name('api.getRtByRw');
Route::get('/api/bangunan-map', [BangunanMapController::class, 'getGeoJson'])->name('api.bangunan.map');
Route::get('/api/locations', [MapController::class, 'getLocationsApi'])->name('api.locations');
