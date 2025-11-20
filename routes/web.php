<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PopulationDataController;
use App\Http\Controllers\AgeRangeController;
use App\Http\Controllers\PopulationEventController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// dashboard utama (harus login & verified)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// semua route yang butuh login
Route::middleware('auth')->group(function () {

    // =========================
    // PETA (GIS MINI)
    // =========================
    Route::get('/peta', [MapController::class, 'index'])
        ->name('map.index');

    // =========================
    // PROFIL USER (Breeze)
    // =========================
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // =========================
    // DATA POPULASI PER WILAYAH
    // =========================

    // list (semua user login boleh lihat)
    Route::get('/data/populasi-per-wilayah', [PopulationDataController::class, 'populasiPerWilayah'])
        ->name('data.populasi');

    // create (admin only)
    Route::get('/data/populasi-per-wilayah/create', [PopulationDataController::class, 'createPopulasiPerWilayah'])
        ->middleware('is_admin')
        ->name('data.populasi.create');

    // store (admin only)
    Route::post('/data/populasi-per-wilayah', [PopulationDataController::class, 'storePopulasiPerWilayah'])
        ->middleware('is_admin')
        ->name('data.populasi.store');

    // edit (admin only)
    Route::get('/data/populasi-per-wilayah/{id}/edit', [PopulationDataController::class, 'editPopulasiPerWilayah'])
        ->middleware('is_admin')
        ->name('data.populasi.edit');

    // update (admin only)
    Route::put('/data/populasi-per-wilayah/{id}', [PopulationDataController::class, 'updatePopulasiPerWilayah'])
        ->middleware('is_admin')
        ->name('data.populasi.update');

    // delete (admin only)
    Route::delete('/data/populasi-per-wilayah/{id}', [PopulationDataController::class, 'destroyPopulasiPerWilayah'])
        ->middleware('is_admin')
        ->name('data.populasi.destroy');

    // =========================
    // DATA RENTANG UMUR
    // =========================

    // list
    Route::get('/data/rentang-umur', [AgeRangeController::class, 'index'])
        ->name('rentang-umur.index');

    // create (admin only)
    Route::get('/data/rentang-umur/create', [AgeRangeController::class, 'create'])
        ->middleware('is_admin')
        ->name('rentang-umur.create');

    // store (admin only)
    Route::post('/data/rentang-umur', [AgeRangeController::class, 'store'])
        ->middleware('is_admin')
        ->name('rentang-umur.store');

    // edit (admin only)
    Route::get('/data/rentang-umur/{id}/edit', [AgeRangeController::class, 'edit'])
        ->middleware('is_admin')
        ->name('rentang-umur.edit');

    // update (admin only)
    Route::put('/data/rentang-umur/{id}', [AgeRangeController::class, 'update'])
        ->middleware('is_admin')
        ->name('rentang-umur.update');

    // delete (admin only)
    Route::delete('/data/rentang-umur/{id}', [AgeRangeController::class, 'destroy'])
        ->middleware('is_admin')
        ->name('rentang-umur.destroy');

    // =========================
    // DATA LAIN (placeholder)
    // =========================
    Route::get('/data/pendidikan-dalam-kk', [PopulationDataController::class, 'pendidikanDalamKK'])
        ->name('data.pendidikan-kk');
    Route::get('/data/pendidikan-ditempuh', [PopulationDataController::class, 'pendidikanDitempuh'])
        ->name('data.pendidikan-ditempuh');
    Route::get('/data/pekerjaan', [PopulationDataController::class, 'pekerjaan'])
        ->name('data.pekerjaan');
    Route::get('/data/agama', [PopulationDataController::class, 'agama'])
        ->name('data.agama');
    Route::get('/data/jenis-kelamin', [PopulationDataController::class, 'jenisKelamin'])
        ->name('data.jenis-kelamin');

    // =========================
    // PERISTIWA KEPENDUDUKAN
    // =========================
    Route::prefix('peristiwa')->group(function () {

        // list semua peristiwa
        Route::get('/', [PopulationEventController::class, 'index'])
            ->name('events.index');

        // halaman pilih jenis peristiwa
        Route::get('/create', [PopulationEventController::class, 'create'])
            ->name('events.create');

        // FORM khusus peristiwa meninggal
        Route::get('/meninggal/create', [PopulationEventController::class, 'createMeninggal'])
            ->name('events.meninggal.create');

        // SIMPAN peristiwa meninggal (POST) ✅
        Route::post('/meninggal/store', [PopulationEventController::class, 'storeMeninggal'])
            ->name('events.meninggal.store');

        // (opsional) route POST umum kalau nanti dibutuhkan
        Route::post('/', [PopulationEventController::class, 'store'])
            ->name('events.store');
    });
});

require __DIR__ . '/auth.php';
