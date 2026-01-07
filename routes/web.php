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
Route::middleware(['auth', 'force_password_change'])->group(function () {

    // =========================
    // CITIZEN EVENTS (LOG)
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

    // create/store/edit/update/delete (admin only)
    Route::get('/data/populasi-per-wilayah/create', [PopulationDataController::class, 'createPopulasiPerWilayah'])
        ->middleware('role:admin')
        ->name('data.populasi.create');

    Route::post('/data/populasi-per-wilayah', [PopulationDataController::class, 'storePopulasiPerWilayah'])
        ->middleware('role:admin')
        ->name('data.populasi.store');

    Route::get('/data/populasi-per-wilayah/{id}/edit', [PopulationDataController::class, 'editPopulasiPerWilayah'])
        ->middleware('role:admin')
        ->name('data.populasi.edit');

    Route::put('/data/populasi-per-wilayah/{id}', [PopulationDataController::class, 'updatePopulasiPerWilayah'])
        ->middleware('role:admin')
        ->name('data.populasi.update');

    Route::delete('/data/populasi-per-wilayah/{id}', [PopulationDataController::class, 'destroyPopulasiPerWilayah'])
        ->middleware('role:admin')
        ->name('data.populasi.destroy');

    // =========================
    // DATA RENTANG UMUR
    // =========================

    // list (semua user login boleh lihat)
    Route::get('/data/rentang-umur', [AgeRangeController::class, 'index'])
        ->name('rentang-umur.index');

    // create/store/edit/update/delete (admin only)
    Route::get('/data/rentang-umur/create', [AgeRangeController::class, 'create'])
        ->middleware('role:admin')
        ->name('rentang-umur.create');

    Route::post('/data/rentang-umur', [AgeRangeController::class, 'store'])
        ->middleware('role:admin')
        ->name('rentang-umur.store');

    Route::get('/data/rentang-umur/{id}/edit', [AgeRangeController::class, 'edit'])
        ->middleware('role:admin')
        ->name('rentang-umur.edit');

    Route::put('/data/rentang-umur/{id}', [AgeRangeController::class, 'update'])
        ->middleware('role:admin')
        ->name('rentang-umur.update');

    Route::delete('/data/rentang-umur/{id}', [AgeRangeController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('rentang-umur.destroy');

    // =========================
    // API LOOKUP CITIZEN (semua login boleh)
    // =========================
    Route::get('/api/citizens/by-nik/{nik}', [CitizenLookupController::class, 'byNik'])
        ->name('api.citizens.byNik');

    Route::get('/api/citizens/search', [CitizenLookupController::class, 'search'])
        ->name('api.citizens.search');

    // =========================
    // DATA LAIN (placeholder) - sementara view-only
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
    // ADMIN - MANAJEMEN AKUN (Opsi A)
    // =========================
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])
            ->name('admin.users.index');

        Route::get('/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])
            ->name('admin.users.create');

        Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])
            ->name('admin.users.store');

        Route::post('/users/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])
            ->name('admin.users.resetPassword');

        Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])
            ->name('admin.users.destroy');
    });

    // =========================
    // PERISTIWA KEPENDUDUKAN
    // =========================
    Route::prefix('peristiwa')->group(function () {

        // list & detail (viewer/operator/admin boleh lihat)
        Route::get('/', [PopulationEventController::class, 'index'])
            ->name('events.index');

        Route::get('/{id}', [PopulationEventController::class, 'show'])
            ->whereNumber('id')
            ->name('events.show');

        // create & store (operator + admin)
        Route::middleware('role:admin,operator')->group(function () {

            // form pilih jenis peristiwa
            Route::get('/create', [PopulationEventController::class, 'create'])
                ->name('events.create');

            // form peristiwa MENINGGAL
            Route::get('/meninggal/create', [PopulationEventController::class, 'createMeninggal'])
                ->name('events.meninggal.create');

            // simpan peristiwa MENINGGAL
            Route::post('/meninggal/store', [PopulationEventController::class, 'storeMeninggal'])
                ->name('events.meninggal.store');

            // (opsional) simpan peristiwa baru umum
            Route::post('/', [PopulationEventController::class, 'store'])
                ->name('events.store');
        });

        // verifikasi (admin only)
        Route::middleware('role:admin')->group(function () {
            Route::post('/{id}/verifikasi', [PopulationEventController::class, 'verify'])
                ->name('events.verify');
        });
    });
});

require __DIR__ . '/auth.php';
