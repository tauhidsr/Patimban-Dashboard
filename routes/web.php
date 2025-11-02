<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PopulationDataController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// dashboard utama (harus login & verified)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// semua route yang butuh login
Route::middleware('auth')->group(function () {

    // profil user (bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ✅ data kependudukan (via controller)
    Route::get('/data/populasi-per-wilayah', [PopulationDataController::class, 'populasiPerWilayah'])->name('data.populasi');

    // ➕ form tambah data populasi per wilayah
    Route::get('/data/populasi-per-wilayah/create', [PopulationDataController::class, 'createPopulasiPerWilayah'])
        ->name('data.populasi.create');

    // 💾 simpan data populasi per wilayah
    Route::post('/data/populasi-per-wilayah', [PopulationDataController::class, 'storePopulasiPerWilayah'])
        ->name('data.populasi.store');

        // ✏️ edit data populasi per wilayah
Route::get('/data/populasi-per-wilayah/{id}/edit', [PopulationDataController::class, 'editPopulasiPerWilayah'])
    ->name('data.populasi.edit');

// 💾 update data populasi per wilayah
Route::put('/data/populasi-per-wilayah/{id}', [PopulationDataController::class, 'updatePopulasiPerWilayah'])
    ->name('data.populasi.update');

    Route::get('/data/rentang-umur', [PopulationDataController::class, 'rentangUmur'])->name('data.rentang-umur');
    Route::get('/data/pendidikan-dalam-kk', [PopulationDataController::class, 'pendidikanDalamKK'])->name('data.pendidikan-kk');
    Route::get('/data/pendidikan-ditempuh', [PopulationDataController::class, 'pendidikanDitempuh'])->name('data.pendidikan-ditempuh');
    Route::get('/data/pekerjaan', [PopulationDataController::class, 'pekerjaan'])->name('data.pekerjaan');
    Route::get('/data/agama', [PopulationDataController::class, 'agama'])->name('data.agama');
    Route::get('/data/jenis-kelamin', [PopulationDataController::class, 'jenisKelamin'])->name('data.jenis-kelamin');
});

require __DIR__.'/auth.php';
