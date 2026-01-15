<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('population_events', function (Blueprint $table) {
            // =========================
            // LAHIR (Versi baru)
            // =========================

            // pengikat utama: ibu
            $table->unsignedBigInteger('ibu_citizen_id')->nullable()->after('citizen_id');
            $table->string('nik_ibu', 20)->nullable()->after('nik');
            $table->string('no_kk_ibu', 20)->nullable()->after('no_kk');
            $table->string('nama_ibu', 150)->nullable()->after('nama');

            // ayah (opsional)
            $table->unsignedBigInteger('ayah_citizen_id')->nullable()->after('ibu_citizen_id');
            $table->string('nik_ayah', 20)->nullable()->after('nik_ibu');
            $table->string('no_kk_ayah', 20)->nullable()->after('no_kk_ibu');
            $table->string('nama_ayah', 150)->nullable()->after('nama_ibu');

            // data bayi (sementara) - tanpa NIK
            $table->string('nama_bayi', 150)->nullable()->after('nama_ayah');
            $table->enum('jenis_kelamin_bayi', ['L', 'P'])->nullable()->after('nama_bayi');
            $table->string('tempat_lahir_bayi', 255)->nullable()->after('tempat_lahir');
            $table->date('tanggal_lahir_bayi')->nullable()->after('tanggal_peristiwa');
            $table->time('jam_lahir_bayi')->nullable()->after('jam_lahir');
            $table->unsignedTinyInteger('anak_ke')->nullable()->after('jam_lahir_bayi');
            $table->decimal('berat_lahir', 5, 2)->nullable()->after('anak_ke');   // kg
            $table->decimal('panjang_lahir', 5, 2)->nullable()->after('berat_lahir'); // cm

            // data pelapor
            $table->string('pelapor', 150)->nullable()->after('tanggal_lapor');
            $table->enum('hubungan_pelapor', ['ayah', 'ibu', 'bidan', 'lainnya'])->nullable()->after('pelapor');

            // status khusus lahir (alur: verifikasi -> menunggu_nik -> selesai)
            $table->enum('status_lahir', ['menunggu_verifikasi', 'menunggu_nik', 'selesai', 'ditolak'])
                ->nullable()
                ->after('status_verifikasi');
        });
    }

    public function down(): void
    {
        Schema::table('population_events', function (Blueprint $table) {
            $table->dropColumn([
                'ibu_citizen_id',
                'nik_ibu',
                'no_kk_ibu',
                'nama_ibu',

                'ayah_citizen_id',
                'nik_ayah',
                'no_kk_ayah',
                'nama_ayah',

                'nama_bayi',
                'jenis_kelamin_bayi',
                'tempat_lahir_bayi',
                'tanggal_lahir_bayi',
                'jam_lahir_bayi',
                'anak_ke',
                'berat_lahir',
                'panjang_lahir',

                'pelapor',
                'hubungan_pelapor',

                'status_lahir',
            ]);
        });
    }
};
