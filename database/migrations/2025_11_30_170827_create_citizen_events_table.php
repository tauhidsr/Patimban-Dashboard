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
        Schema::create('citizen_events', function (Blueprint $table) {
            $table->id();

            // relasi ke warga (opsional nullable dulu, biar fleksibel)
            $table->foreignId('citizen_id')
                ->nullable()
                ->constrained('citizens')
                ->nullOnDelete();

            // identitas dasar saat peristiwa (di-copy biar historis)
            $table->string('nik', 16)->nullable();
            $table->string('nama')->nullable();

            // jenis peristiwa (datang, pindah, meninggal, lahir, dll)
            $table->string('jenis_peristiwa'); 
            // contoh value: datang, pindah, meninggal, lahir, hilang, lapor_1x24

            // tanggal & keterangan
            $table->date('tanggal_peristiwa')->nullable();
            $table->text('keterangan')->nullable();

            // wilayah saat peristiwa
            $table->string('dusun')->nullable();
            $table->string('rw')->nullable();
            $table->string('rt')->nullable();

            // status verifikasi (admin verifikasi)
            $table->string('status_verifikasi')->default('pending');
            // contoh: pending, disetujui, ditolak

            // siapa yang input
            $table->foreignId('created_by')->nullable(); // user id
            $table->foreignId('verified_by')->nullable(); // admin id

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citizen_events');
    }
};
