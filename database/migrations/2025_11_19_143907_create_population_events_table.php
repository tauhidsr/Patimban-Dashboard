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

            /**
             * =========================================================
             * RELASI KE MASTER PENDUDUK
             * =========================================================
             * Untuk peristiwa umum: bisa simpan citizen_id (warga yang mengalami peristiwa)
             * Untuk peristiwa LAHIR: bayi belum punya NIK => citizen_id bayi biasanya NULL
             */
            $table->unsignedBigInteger('citizen_id')->nullable();

            /**
             * Identitas dasar saat peristiwa (legacy/umum).
             * Untuk lahir versi baru, field ini tidak wajib dipakai untuk bayi.
             */
            $table->string('nik', 20)->nullable();
            $table->string('no_kk', 20)->nullable();
            $table->string('nama', 150)->nullable();

            /**
             * Jenis peristiwa
             */
            $table->enum('jenis_peristiwa', [
                'lahir',
                'datang',
                'pindah',
                'meninggal',
                'hilang',
                'sementara_masuk',
                'sementara_keluar',
            ]);

            /**
             * Lokasi domisili saat peristiwa (opsional; saat ini masih null)
             */
            $table->unsignedBigInteger('dusun_id')->nullable();
            $table->unsignedBigInteger('rw_id')->nullable();
            $table->unsignedBigInteger('rt_id')->nullable();

            /**
             * Waktu peristiwa & pelaporan
             */
            $table->date('tanggal_peristiwa')->nullable();
            $table->date('tanggal_lapor')->nullable();

            /**
             * Catatan umum peristiwa
             */
            $table->text('catatan_peristiwa')->nullable();

            /**
             * Siapa yang input
             */
            $table->unsignedBigInteger('created_by')->nullable();

            /**
             * Status verifikasi oleh admin desa
             */
            $table->enum('status_verifikasi', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('catatan_verifikasi')->nullable();

            /**
             * =========================================================
             * EXTRA: VERIFIER INFO (sesuai model kamu)
             * =========================================================
             */
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();

            /**
             * =========================================================
             * EXTRA: APPLY/REVERT TRACKING (sesuai verify() kamu)
             * =========================================================
             */
            $table->string('previous_status_dasar', 50)->nullable();
            $table->timestamp('status_applied_at')->nullable();
            $table->unsignedBigInteger('status_applied_by')->nullable();

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

            /**
             * =========================================================
             * -------- LAHIR (REVISI SESUAI SARAN SULTAN) --------
             * =========================================================
             * Bayi belum punya NIK, jadi simpan data bayi sementara.
             * Pengikat utama: IBU (nik_ibu -> citizens).
             * AYAH opsional.
             */

            // Pengikat utama: IBU
            $table->unsignedBigInteger('ibu_citizen_id')->nullable();
            $table->string('nik_ibu', 20)->nullable();

            // Ayah opsional
            $table->unsignedBigInteger('ayah_citizen_id')->nullable();
            $table->string('nik_ayah', 20)->nullable();

            // Data bayi (sementara)
            $table->string('nama_bayi', 150)->nullable();
            $table->enum('jenis_kelamin_bayi', ['L', 'P'])->nullable();
            $table->string('tempat_lahir_bayi')->nullable();
            $table->date('tanggal_lahir_bayi')->nullable();
            $table->time('jam_lahir_bayi')->nullable();
            $table->unsignedInteger('anak_ke')->nullable();
            $table->decimal('berat_lahir', 5, 2)->nullable();   // kg, contoh 3.25
            $table->decimal('panjang_lahir', 5, 2)->nullable(); // cm, contoh 49.50

            // Data pelapor
            $table->string('pelapor')->nullable();
            $table->enum('hubungan_pelapor', ['ayah', 'ibu', 'bidan', 'lainnya'])->nullable();

            // Status khusus lahir
            $table->enum('status_lahir', ['menunggu_verifikasi', 'menunggu_nik', 'sudah_jadi_citizen'])
                ->default('menunggu_verifikasi');

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

            /**
             * (Penduduk sementara 1x24 jam pakai tabel lain:
             *  temporary_residents. Peristiwa tetap dicatat di sini
             *  dengan jenis: 'sementara_masuk' / 'sementara_keluar')
             */

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
