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
            // DATANG (extended fields)
            // =========================

            if (!Schema::hasColumn('population_events', 'tanggal_datang')) {
                $table->date('tanggal_datang')->nullable()->after('tanggal_peristiwa');
            }

            // NOTE:
            // Di schema awal kamu sudah ada:
            // - asal_datang_kategori
            // - alamat_asli
            // - alasan_datang
            // Jadi di migration tambahan ini kita fokus ke field asal/tujuan yang lebih detail.

            if (!Schema::hasColumn('population_events', 'alamat_asal')) {
                // taruh setelah alamat_asli kalau ada, kalau tidak ya tetap ditambahkan (tanpa after)
                if (Schema::hasColumn('population_events', 'alamat_asli')) {
                    $table->text('alamat_asal')->nullable()->after('alamat_asli');
                } else {
                    $table->text('alamat_asal')->nullable();
                }
            }

            if (!Schema::hasColumn('population_events', 'desa_asal')) {
                $table->string('desa_asal')->nullable();
            }
            if (!Schema::hasColumn('population_events', 'kecamatan_asal')) {
                $table->string('kecamatan_asal')->nullable();
            }
            if (!Schema::hasColumn('population_events', 'kabupaten_asal')) {
                $table->string('kabupaten_asal')->nullable();
            }
            if (!Schema::hasColumn('population_events', 'provinsi_asal')) {
                $table->string('provinsi_asal')->nullable();
            }

            // tujuan (Patimban)
            if (!Schema::hasColumn('population_events', 'alamat_sekarang_tujuan')) {
                $table->text('alamat_sekarang_tujuan')->nullable();
            }
            if (!Schema::hasColumn('population_events', 'dusun_tujuan')) {
                $table->string('dusun_tujuan')->nullable();
            }
            if (!Schema::hasColumn('population_events', 'rw_tujuan')) {
                $table->string('rw_tujuan')->nullable();
            }
            if (!Schema::hasColumn('population_events', 'rt_tujuan')) {
                $table->string('rt_tujuan')->nullable();
            }

            // atribut datang
            if (!Schema::hasColumn('population_events', 'status_datang')) {
                $table->enum('status_datang', ['tetap', 'sementara'])->nullable();
            }
            if (!Schema::hasColumn('population_events', 'rencana_tinggal')) {
                $table->string('rencana_tinggal')->nullable();
            }

            // IMPORTANT:
            // pelapor & hubungan_pelapor SUDAH ADA DI CREATE TABLE,
            // jadi jangan ditambah lagi di sini.
        });
    }

    public function down(): void
    {
        Schema::table('population_events', function (Blueprint $table) {

            $cols = [];

            foreach ([
                'tanggal_datang',
                'alamat_asal',
                'desa_asal',
                'kecamatan_asal',
                'kabupaten_asal',
                'provinsi_asal',
                'alamat_sekarang_tujuan',
                'dusun_tujuan',
                'rw_tujuan',
                'rt_tujuan',
                'status_datang',
                'rencana_tinggal',
            ] as $c) {
                if (Schema::hasColumn('population_events', $c)) {
                    $cols[] = $c;
                }
            }

            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
