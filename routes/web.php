<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PopulationDataController;
use App\Http\Controllers\AgeRangeController; // ⭐ DITAMBAHKAN
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

    // route gis
    Route::get('/peta', [MapController::class, 'index'])->name('map.index');

    // profil user (bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // DATA KEPENDUDUKAN (VIA CONTROLLER)

    // LIST data populasi per wilayah (boleh semua user yang login)
    Route::get('/data/populasi-per-wilayah', [PopulationDataController::class, 'populasiPerWilayah'])
        ->name('data.populasi');

    // tambah data (kependudukan/admin)
    Route::get('/data/populasi-per-wilayah/create', [PopulationDataController::class, 'createPopulasiPerWilayah'])
        ->middleware('is_admin')
        ->name('data.populasi.create');

    // simpan data (kependudukan/admin)
    Route::post('/data/populasi-per-wilayah', [PopulationDataController::class, 'storePopulasiPerWilayah'])
        ->middleware('is_admin')
        ->name('data.populasi.store');

    // edit data (kependudukan/admin)
    Route::get('/data/populasi-per-wilayah/{id}/edit', [PopulationDataController::class, 'editPopulasiPerWilayah'])
        ->middleware('is_admin')
        ->name('data.populasi.edit');

    // update data (kependudukan/admin)
    Route::put('/data/populasi-per-wilayah/{id}', [PopulationDataController::class, 'updatePopulasiPerWilayah'])
        ->middleware('is_admin')
        ->name('data.populasi.update');

    // hapus data (kependudukan/admin)
    Route::delete('/data/populasi-per-wilayah/{id}', [PopulationDataController::class, 'destroyPopulasiPerWilayah'])
        ->middleware('is_admin')
        ->name('data.populasi.destroy');

    // DATA RENTANG UMUR (VIA CONTROLLER)

    // LIST rentang umur (boleh semua user login)
    Route::get('/data/rentang-umur', [AgeRangeController::class, 'index'])
        ->name('rentang-umur.index');

    // tambah data (rentang umur/admin)
    Route::get('/data/rentang-umur/create', [AgeRangeController::class, 'create'])
        ->middleware('is_admin')
        ->name('rentang-umur.create');

    // simpan data (rentang umur/admin)
    Route::post('/data/rentang-umur', [AgeRangeController::class, 'store'])
        ->middleware('is_admin')
        ->name('rentang-umur.store');

    // edit data (rentang umur/admin)
    Route::get('/data/rentang-umur/{id}/edit', [AgeRangeController::class, 'edit'])
        ->middleware('is_admin')
        ->name('rentang-umur.edit');

    // update data (rentang umur/admin)
    Route::put('/data/rentang-umur/{id}', [AgeRangeController::class, 'update'])
        ->middleware('is_admin')
        ->name('rentang-umur.update');

    // hapus data (rentang umur/admin)
    Route::delete('/data/rentang-umur/{id}', [AgeRangeController::class, 'destroy'])
        ->middleware('is_admin')
        ->name('rentang-umur.destroy');

    // route lain (masih placeholder)
    Route::get('/data/pendidikan-dalam-kk', [PopulationDataController::class, 'pendidikanDalamKK'])->name('data.pendidikan-kk');
    Route::get('/data/pendidikan-ditempuh', [PopulationDataController::class, 'pendidikanDitempuh'])->name('data.pendidikan-ditempuh');
    Route::get('/data/pekerjaan', [PopulationDataController::class, 'pekerjaan'])->name('data.pekerjaan');
    Route::get('/data/agama', [PopulationDataController::class, 'agama'])->name('data.agama');
    Route::get('/data/jenis-kelamin', [PopulationDataController::class, 'jenisKelamin'])->name('data.jenis-kelamin');
});

require __DIR__ . '/auth.php';
