<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DemografiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\KomoditasPublikController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PendudukController;
use App\Http\Controllers\Admin\RtRwController;
use App\Http\Controllers\Admin\KomoditasController;
use App\Http\Controllers\Admin\BangunanController;
use App\Http\Controllers\Api\WilayahController;
use App\Http\Controllers\Api\BangunanMapController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/demografi', [DemografiController::class, 'index'])->name('demografi.index');
Route::get('/peta-sebaran', [MapController::class, 'index'])->name('peta.publik');
Route::get('/komoditas', [KomoditasPublikController::class, 'index'])->name('komoditas.publik');
Route::get('/peta-wilayah', [MapController::class, 'index'])->name('peta.index');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('penduduk', PendudukController::class);
    Route::resource('komoditas', KomoditasController::class)->except(['show']);
    Route::resource('bangunan', BangunanController::class)->except(['show']);

    Route::prefix('rt-rw')->name('rt-rw.')->group(function () {
        Route::get('/', [RtRwController::class, 'index'])->name('index');
        Route::get('/create', [RtRwController::class, 'create'])->name('create');
        Route::post('/', [RtRwController::class, 'store'])->name('store');
        Route::get('/{type}/{id}/edit', [RtRwController::class, 'edit'])->name('edit');
        Route::put('/{type}/{id}', [RtRwController::class, 'update'])->name('update');
        Route::delete('/{type}/{id}', [RtRwController::class, 'destroy'])->name('destroy');
    });
});

Route::get('/api/get-rt-by-rw/{rw_id}', [WilayahController::class, 'getRtByRw'])->name('api.getRtByRw');
Route::get('/api/bangunan-map', [BangunanMapController::class, 'getGeoJson'])->name('api.bangunan.map');
Route::get('/api/locations', [MapController::class, 'getLocationsApi'])->name('api.locations');