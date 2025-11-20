<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('population_events', function (Blueprint $table) {
            $table->id();

            // relasi ke master penduduk (boleh null kalau belum ada di master)
            $table->unsignedBigInteger('citizen_id')->nullable();

            // identitas dasar saat peristiwa
            $table->string('nik', 20)->nullable();   // ⬅️ TADINYA "nuk", SEKARANG "nik"
            $table->string('no_kk', 20)->nullable();
            $table->string('nama', 150)->nullable();

            // jenis peristiwa
            $table->enum('jenis_peristiwa', [
                'lahir',
                'datang',
                'pindah',
                'meninggal',
                'hilang',
                'sementara_masuk',
                'sementara_keluar',
            ]);

            // lokasi domisili saat peristiwa
            $table->unsignedBigInteger('dusun_id')->nullable();
            $table->unsignedBigInteger('rw_id')->nullable();
            $table->unsignedBigInteger('rt_id')->nullable();

            // waktu peristiwa & pelaporan
            $table->date('tanggal_peristiwa')->nullable();
            $table->date('tanggal_lapor')->nullable();

            // catatan umum peristiwa
            $table->text('catatan_peristiwa')->nullable();

            // siapa yang input
            $table->unsignedBigInteger('created_by')->nullable();

            // status verifikasi oleh admin desa
            $table->enum('status_verifikasi', ['menunggu', 'disetujui', 'ditolak'])
                ->default('menunggu');
            $table->text('catatan_verifikasi')->nullable();

            // -------- MENINGGAL --------
            $table->string('tempat_meninggal')->nullable();
            $table->time('jam_kematian')->nullable();
            $table->enum('penyebab_kematian', [
                'sakit_biasa_tua',
                'wabah_penyakit',
                'kecelakaan',
                'kriminalitas',
                'bunuh_diri',
                'lainnya',
            ])->nullable();

            $table->enum('yang_menyatakan_kematian', [
                'dokter',
                'tenaga_kesehatan',
                'kepolisian',
                'lainnya',
            ])->nullable();
            $table->string('nomor_akta_kematian')->nullable();
            $table->string('file_akta_kematian_path')->nullable();

            // -------- PINDAH --------
            $table->enum('tujuan_pindah', [
                'keluar_desa',
                'keluar_kecamatan',
                'keluar_kabupaten',
                'keluar_provinsi',
                'keluar_negeri',
            ])->nullable();
            $table->text('alamat_tujuan')->nullable();

            // -------- LAHIR --------
            $table->string('tempat_lahir')->nullable();
            $table->time('jam_lahir')->nullable();
            $table->enum('penolong_kelahiran', [
                'dokter',
                'bidan',
                'tenaga_kesehatan',
                'keluarga',
                'lainnya',
            ])->nullable();

            // -------- DATANG --------
            $table->enum('asal_datang_kategori', [
                'dalam_kecamatan',
                'luar_kecamatan',
                'luar_kabupaten',
                'luar_provinsi',
                'luar_negeri',
            ])->nullable();
            $table->text('alamat_asli')->nullable();
            $table->string('alasan_datang')->nullable();

            // (Penduduk sementara 1x24 jam pakai tabel lain:
            //  temporary_residents. Peristiwa tetap dicatat di sini
            //  dengan jenis: 'sementara_masuk' / 'sementara_keluar')

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('population_events');
    }
};