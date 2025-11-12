<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PopulationDataController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// dashboard utama (harus login & verified)
Route::get('/dashboard', [DashboardController::class,'index'])
->middleware(['auth','verified'])
->name('dashboard');

// semua route yang butuh login
Route::middleware('auth')->group(function () {

    // profil user (bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ✅ data kependudukan (via controller)

    // LIST data populasi per wilayah (boleh semua user yang login)
    Route::get('/data/populasi-per-wilayah', [PopulationDataController::class, 'populasiPerWilayah'])
        ->name('data.populasi');

    // ➕ form tambah data (admin only)
    Route::get('/data/populasi-per-wilayah/create', [PopulationDataController::class, 'createPopulasiPerWilayah'])
        ->middleware('is_admin')
        ->name('data.populasi.create');

    // 💾 simpan data baru (admin only)
    Route::post('/data/populasi-per-wilayah', [PopulationDataController::class, 'storePopulasiPerWilayah'])
        ->middleware('is_admin')
        ->name('data.populasi.store');

    // ✏️ form edit (admin only)
    Route::get('/data/populasi-per-wilayah/{id}/edit', [PopulationDataController::class, 'editPopulasiPerWilayah'])
        ->middleware('is_admin')
        ->name('data.populasi.edit');

    // 💾 update data (admin only)
    Route::put('/data/populasi-per-wilayah/{id}', [PopulationDataController::class, 'updatePopulasiPerWilayah'])
        ->middleware('is_admin')
        ->name('data.populasi.update');

    // 🗑️ hapus data (admin only)
    Route::delete('/data/populasi-per-wilayah/{id}', [PopulationDataController::class, 'destroyPopulasiPerWilayah'])
        ->middleware('is_admin')
        ->name('data.populasi.destroy');

    // route lain (masih placeholder)
    Route::get('/data/rentang-umur', [PopulationDataController::class, 'rentangUmur'])->name('data.rentang-umur');
    Route::get('/data/pendidikan-dalam-kk', [PopulationDataController::class, 'pendidikanDalamKK'])->name('data.pendidikan-kk');
    Route::get('/data/pendidikan-ditempuh', [PopulationDataController::class, 'pendidikanDitempuh'])->name('data.pendidikan-ditempuh');
    Route::get('/data/pekerjaan', [PopulationDataController::class, 'pekerjaan'])->name('data.pekerjaan');
    Route::get('/data/agama', [PopulationDataController::class, 'agama'])->name('data.agama');
    Route::get('/data/jenis-kelamin', [PopulationDataController::class, 'jenisKelamin'])->name('data.jenis-kelamin');
});

require __DIR__.'/auth.php';
