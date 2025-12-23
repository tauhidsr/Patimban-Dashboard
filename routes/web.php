<?php

use App\Http\Controllers\CitizenLookupController;
use App\Http\Controllers\CitizenEventController;
use App\Http\Controllers\CitizenController;
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
    // CITIZENEVENTCONTROLLER
    // =========================
    Route::get('/citizen-events', [CitizenEventController::class, 'index'])
        ->name('citizen-events.index');

    // =========================
    // MASTER WARGA (READ ONLY)
    // =========================
    Route::get('/citizens', [CitizenController::class, 'index'])
        ->name('citizens.index');

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

    Route::get('/api/citizens/by-nik/{nik}', [CitizenLookupController::class, 'byNik'])
        ->name('api.citizens.byNik');

    Route::get('/api/citizens/search', [CitizenLookupController::class, 'search'])
        ->name('api.citizens.search');

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
    // modul peristiwa kependudukan
    Route::prefix('peristiwa')->group(function () {
        // list semua peristiwa
        Route::get('/', [\App\Http\Controllers\PopulationEventController::class, 'index'])
            ->name('events.index');

        // form pilih jenis peristiwa (nanti bisa ada lahir/datang/dll)
        Route::get('/create', [\App\Http\Controllers\PopulationEventController::class, 'create'])
            ->name('events.create');

        // form peristiwa MENINGGAL
        Route::get('/meninggal/create', [\App\Http\Controllers\PopulationEventController::class, 'createMeninggal'])
            ->name('events.meninggal.create');

        // simpan peristiwa MENINGGAL
        Route::post('/meninggal/store', [\App\Http\Controllers\PopulationEventController::class, 'storeMeninggal'])
            ->name('events.meninggal.store');

        // (opsional) simpan peristiwa baru umum
        Route::post('/', [\App\Http\Controllers\PopulationEventController::class, 'store'])
            ->name('events.store');

        // aksi verifikasi (KHUSUS ADMIN)
        Route::middleware('is_admin')->group(function () {
            Route::post('/{id}/verifikasi', [\App\Http\Controllers\PopulationEventController::class, 'verify'])
                ->name('events.verify');
        });

        // halaman detail peristiwa
        Route::get('/{id}', [\App\Http\Controllers\PopulationEventController::class, 'show'])
            ->name('events.show');
    });
});

require __DIR__ . '/auth.php';
